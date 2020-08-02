package com.cpdms.service.personnel;

import cn.hutool.core.io.FileUtil;
import cn.hutool.core.util.RandomUtil;
import cn.hutool.json.JSONObject;
import cn.hutool.poi.excel.ExcelReader;
import cn.hutool.poi.excel.ExcelUtil;
import com.cpdms.common.conf.UnionpayConfig;
import com.cpdms.common.conf.V2Config;
import com.cpdms.mapper.personnel.BasePersonnelMapper;
import com.cpdms.model.personnel.BasePersonnel;
import com.cpdms.util.*;
import com.google.gson.Gson;
import org.bouncycastle.util.encoders.Base64;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.scheduling.annotation.Async;
import org.springframework.stereotype.Service;

import java.io.File;
import java.security.KeyFactory;
import java.security.MessageDigest;
import java.security.PrivateKey;
import java.security.Signature;
import java.security.spec.PKCS8EncodedKeySpec;
import java.util.*;

@Service
public class AsyncService {
    private static Logger logger = LoggerFactory.getLogger(AsyncService.class);
    @Autowired
    private BasePersonnelMapper basePersonnelMapper;
    public static final String TRXTP_ADD = "0";
    public static final String TRXTP_MODIFY = "1";

    // 处理excel和特征值
    @Async
    public void dealZip(String filepath) {
        int count = 0;
        try {
            // 先解压zip文件
            List<String> files = ZipUtils.ZipContraMultiFile(filepath);
            if (files.size() == 0) {
                return;
            }
            // 分离xecel和jpg文件
            List<String> jpgFiles = new ArrayList<>();
            String excelFile = "";
            for (int i = 0;i<files.size();i++) {
                if (files.get(i).endsWith("jpg")) {
                    jpgFiles.add(files.get(i));
                } else if (files.get(i).endsWith("bankerInfos.xls") || files.get(i).endsWith("bankerInfos.xlsx")) {
                    excelFile = files.get(i);
                }
            }
            if (excelFile.equals("")) {
                return;
            }
            // 读取excel
            List<BasePersonnel> basePersonnels = readExcel(excelFile);
            if (basePersonnels == null || basePersonnels.size() == 0) {
                return;
            }
            // 读取图片特征值
            Map<String, String> calcResults = new HashMap<>();

            for (int i=0;i<jpgFiles.size();i++) {
                String key = FileUtil.getName(jpgFiles.get(i));
                String calcResult = ImgCalcUtils.calcByGo(V2Config.getPersonnel_dir() + File.separator + key);
                calcResults.put(key, calcResult);
            }
            // 插入数据库
            for (int i=0;i<basePersonnels.size();i++) {
                BasePersonnel basePersonnel = basePersonnels.get(i);
                // 检查一下是否数据库有重复
                List<BasePersonnel> exists = basePersonnelMapper.selectExists(basePersonnel.getCardNo(), basePersonnel.getPersonnelNo(), basePersonnel.getPersonnelMobile());
                if (exists.size() > 0) {
                    continue;
                }
                basePersonnel.setId(SnowflakeIdWorker.getUUID());
                basePersonnel.setEntryTime(new Date());
                String calcResult = calcResults.get(basePersonnel.getPhotoName() + ".jpg");
                if (calcResult != null && !calcResult.equals("")) {
                    basePersonnel.setCalcResult(1);
                    basePersonnel.setCalcValue(calcResult);
                } else {
                    basePersonnel.setCalcResult(2);
                    basePersonnel.setCalcValue("");
                }

				if (basePersonnel.getCalcResult() == 1) {
					JSONObject result = syncPersonnel(basePersonnel, TRXTP_ADD);
					if (result.get("code") != null) {
					    basePersonnel.setRespCode(result.getStr("code"));
					    basePersonnel.setRespMsg(result.getStr("msg"));
					    if (result.getStr("code").equals("1000")) {
                            basePersonnel.setSyncStatus(1);
                        }
                    }
				}
                basePersonnelMapper.insertSelective(basePersonnel);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        logger.info("成功导入" + count + "条行员信息");

        return;
    }

    /*
     * @Description 信息同步接口
     * @Date 2020/3/5 13:01
     * @param basePersonnel
     * @param trxTp
     *@return cn.hutool.json.JSONObject
     **/
    public JSONObject syncPersonnel(BasePersonnel basePersonnel, String trxTp) {
        Map<String, String> params = setCommonParams(basePersonnel);
        Map<String, String> postContent = new HashMap<>(0);
        params.put("feature", basePersonnel.getCalcValue());
        params.put("feaVersion", "1.2.0");
        //获取sign (参数按照transDtTm+transSeq+cardNo+staffNo+name+feature+mobile顺序字符串连接
        //然后使用SHA256withRSA进行签名)
        String value = params.get("transDtTm") + params.get("transSeq") + params.get("cardNo")
                + params.get("staffNo") + params.get("name") + md5(params.get("feature"))
                + params.get("mobile");
        postContent.put("sign", sign(value));
        //0新增 1修改
        postContent.put("trxTp", trxTp);
        postContent.put("data", getJsonString(params));
        //发送请求
        return callApi(UnionpayConfig.getUserSynUrl(), postContent);
    }

    /**
     * @Description 用户关闭接口
     * @Date 2020/3/5 13:05
     * @param basePersonnel
     *@return cn.hutool.json.JSONObject
     **/
    public JSONObject userClose(BasePersonnel basePersonnel) {
        Map<String, String> params = setCommonParams(basePersonnel);
        Map<String, String> postContent = new HashMap<>(0);
        //获取sign (参数按照transDtTm+transSeq+cardNo+staffNo+name+mobile顺序字符串连接
        //然后使用SHA256withRSA进行签名)
        String value = params.get("transDtTm") + params.get("transSeq") + params.get("cardNo")
                + params.get("staffNo") + params.get("name") + params.get("mobile");
        postContent.put("sign", sign(value));
        //0新增 1修改
        postContent.put("data", getJsonString(params));
        //发送请求
        return callApi(UnionpayConfig.getCloseSerUrl(), postContent);
    }

    /*
     * @Description 获取json字符串
     * @Date 2020/3/5 13:12
     * @param params
     *@return java.lang.String
     **/
    private String getJsonString(Map<String, String> params) {
        Gson gson = new Gson();
        String data = "";
        try {
            data = gson.toJson(params);
        } catch (Exception e) {
            logger.info("序列化失败：" + e.getMessage());
        }

        return data;
    }

    /*
     * @Description 共用参数设置
     * @Date 2020/3/5 13:07
     * @param basePersonnel
     *@return java.util.Map<java.lang.String,java.lang.String>
     **/
    private Map<String, String> setCommonParams(BasePersonnel basePersonnel) {
        Map<String, String> params = new HashMap<>(0);
        //生成时间戳
        String timestamp = DateUtils.format(new Date(), "YYYYMMDDHHMMSS");
        params.put("transDtTm", timestamp);
        params.put("transSeq", RandomUtil.randomNumbers(6));
        params.put("cardNo", basePersonnel.getCardNo());
        params.put("staffNo", basePersonnel.getPersonnelNo());
        params.put("name", basePersonnel.getDeptName());
        params.put("mobile", basePersonnel.getPersonnelMobile());

        return params;
    }

    /*
     * @Description md5
     * @Date 2020/3/5 13:01
     * @param value
     *@return java.lang.String
     **/
    private String md5(String value) {
        try {
            MessageDigest md = MessageDigest.getInstance("MD5");
            md.update(value.getBytes("UTF-8"));
            return new String(md.digest(), "UTF-8");
        }catch (Exception e) {
            logger.info("MD5摘要失败" + value);
            e.printStackTrace();
        }
        return "";
    }

    /*
     * @Description 签名函数
     * @Date 2020/3/5 13:01
     * @param value
     *@return java.lang.String
     **/
    private String sign(String value) {
        try {
            byte[] keyBytes = Base64.decode(UnionpayUtils.getNjPrivateKey());
            PKCS8EncodedKeySpec keySpec = new PKCS8EncodedKeySpec(keyBytes);
            KeyFactory keyf = KeyFactory.getInstance("RSA");
            PrivateKey priKey = keyf.generatePrivate(keySpec);
            Signature signature = Signature.getInstance("SHA256WithRSA");
            signature.initSign(priKey);
            signature.update(value.getBytes());
            byte[] signed = signature.sign();

            return Base64.toBase64String(signed);
        } catch (Exception e) {
            logger.info("签名失败" + value);
            e.printStackTrace();
        }

        return "";
    }

    /*
     * @Description API请求
     * @Date 2020/3/5 13:00
     * @param url
     * @param params 
     *@return cn.hutool.json.JSONObject
     **/
    private JSONObject callApi(String url, Map<String, String> params) {
        params.put("accessId", "njrhpay");
        params.put("accessKey", "4fc82b26aecb47d2868c4efbe3581732a3e7cbcc6c2efb32062c08170a05eeb8");
        String resultStr = UnionpayUtils.sendPostGson(url, params);
        logger.info("接口调用参数：" + params);
        JSONObject jsonObj = new JSONObject(resultStr);
        logger.info("接口调用返回：" + resultStr);

        return jsonObj;
    }

    /*
     * @Description 读取excel
     * @Param String 文件名
     * */
    private List<BasePersonnel> readExcel(String file) {
        ExcelReader reader = ExcelUtil.getReader(FileUtil.file(file), 0);
        List<List<Object>> rows = reader.read(1);
        List<BasePersonnel> basePersonnels = new ArrayList<>();
        if (rows.size() == 0) {
            return null;
        }
        for (int i=0;i<rows.size();i++) {
            BasePersonnel basePersonnel = new BasePersonnel();
            basePersonnel.setPersonnelNo(rows.get(i).get(0).toString());
            basePersonnel.setPersonnelName(rows.get(i).get(1).toString());
            basePersonnel.setPersonnelMobile(rows.get(i).get(2).toString());
            basePersonnel.setCardNo(rows.get(i).get(3).toString());
            basePersonnel.setPhotoName(rows.get(i).get(4).toString());
            basePersonnel.setUnitName(rows.get(i).get(5).toString());
            basePersonnel.setDeptName(rows.get(i).get(6).toString());
            basePersonnels.add(basePersonnel);
        }

        return basePersonnels;
    }
}
