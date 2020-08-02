package com.cpdms.controller.dinning;

import java.math.BigDecimal;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import com.cpdms.common.token.ApiTokenValid;
import com.cpdms.model.auto.TSysDept;
import com.cpdms.model.dinning.BizOrdPay;
import com.cpdms.model.dinning.BizOrder;
import com.cpdms.model.payment.BizPayment;
import com.cpdms.service.SysDeptService;
import com.cpdms.service.dinning.BizOrdPayService;
import com.cpdms.service.dinning.BizOrderService;
import com.cpdms.service.payment.BizPaymentService;
import com.cpdms.util.*;
import com.google.gson.Gson;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.transaction.annotation.Transactional;
import org.springframework.web.bind.annotation.*;

import com.cpdms.common.base.BaseController;
import com.cpdms.common.conf.UnionpayConfig;
import com.cpdms.common.domain.AjaxResult;
import com.cpdms.model.dinning.BaseEmp;
import com.cpdms.service.dinning.BaseEmpService;

import cn.hutool.json.JSONObject;

@Controller
@RequestMapping("/unionpay")

public class UnionpayController extends BaseController {
    private static Logger logger = LoggerFactory.getLogger(UnionpayController.class);
    @Autowired
    private BaseEmpService baseEmpService;
    @Autowired
    private BizOrderService bizOrderService;
    @Autowired
    private BizOrdPayService bizOrdPayService;
    @Autowired
    private BizPaymentService bizPaymentService;
    @Autowired
    private SysDeptService sysDeptService;

    private static final String NanjingSysId = "A338AG8LB1";
    private static final String BeijingSysId = "A364GL5CE2";
    private static final String NjpxSysId = "A349AG8LA1";

    // 接口请求backendToken
    private Boolean createBackendToken() {
        Map<String, String> params = new HashMap<>(0);
        params.put("appId", UnionpayConfig.getAppId());
        params.put("secret", UnionpayConfig.getSecret());
        //生成随即字符串
        String nonceStr = UnionpayUtils.createNonceStr();
        params.put("nonceStr", nonceStr);
        //生成时间戳
        String timestamp = String.valueOf(System.currentTimeMillis() / 1000);
        params.put("timestamp", timestamp);
        //签名 (appId,secret,nonceStr,timestamp)
        String value = UnionpayUtils.sortMap(params);
        String signature = UnionpayUtils.sha256(value.getBytes());
        params.put("signature", signature);
        //发送请求
        JSONObject result = callApi(UnionpayConfig.getGetBackendTokenUrl(), params);
        if (result == null) {
            return false;
        }
        UnionpayConfig.setBackendToken(result.getStr("backendToken"));
        long expiresIn = result.getLong("expiresIn");
        UnionpayConfig.setBackendTokenExprireTime(new Date(System.currentTimeMillis() + 300 * 1000));

        return true;
    }

    // 接口请求frontToken
    private Boolean createFrontToken() {
        Map<String, String> params = new HashMap<String, String>(0);
        params.put("appId", UnionpayConfig.getAppId());
        params.put("secret", UnionpayConfig.getSecret());
        //生成随即字符串
        String nonceStr = UnionpayUtils.createNonceStr();
        params.put("nonceStr", nonceStr);
        //生成时间戳
        String timestamp = String.valueOf(System.currentTimeMillis() / 1000);
        params.put("timestamp", timestamp);
        //签名 (appId,secret,nonceStr,timestamp)
        String value = UnionpayUtils.sortMap(params);
        String signature = UnionpayUtils.sha256(value.getBytes());
        params.put("signature", signature);
        //发送请求
        JSONObject result = callApi(UnionpayConfig.getGetFrontTokenUrl(), params);
        if (result == null) {
            return false;
        }
        UnionpayConfig.setFrontToken(result.getStr("frontToken"));
        long expiresIn = result.getLong("expiresIn");
        //UnionpayConfig.setFrontTokenExprireTime(new Date(System.currentTimeMillis() + expiresIn * 1000));
        UnionpayConfig.setFrontTokenExprireTime(new Date(System.currentTimeMillis() + 300 * 1000));

        return true;
    }

    // 获取backendToken
    private String getBackendToken() {
        // 如果token不存在，或者token已过期，就重新请求一个
        if (UnionpayConfig.getBackendToken() == null || UnionpayConfig.getBackendTokenExprireTime().before(new Date())) {
            if (!createBackendToken()) {
                return "";
            }
        }
        return UnionpayConfig.getBackendToken();
    }

    // 获取frontToken
    @PostMapping(value = "getFrontToken", produces = "application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public AjaxResult getFrontToken() {
        // 如果token不存在，或者token已过期，就重新请求一个
        if (UnionpayConfig.getFrontToken() == null || UnionpayConfig.getFrontTokenExprireTime().before(new Date())) {
            if (!createFrontToken()) {
                return error(402, "获取失败");
            }
        }
        Map<String, Object> result = new HashMap<String, Object>();
        result.put("token", UnionpayConfig.getFrontToken());
        result.put("expired_time", UnionpayConfig.getFrontTokenExprireTime());
        return retobject(200, result);
    }

    private JSONObject callApi(String url, Map<String, String> params) {
        String resultStr = UnionpayUtils.sendPostGson(url, params);
        logger.info("接口调用参数：" + params);
        JSONObject jsonObj = new JSONObject(resultStr);
        logger.info("接口调用返回：" + resultStr);
        if (!"00".equals(jsonObj.getStr("resp"))) {
            logger.error("接口调用失败，错误信息：" + jsonObj.getStr("msg"));
            return null;
        }

        return jsonObj.getJSONObject("params");
    }

    // 获取accessToken
    @PostMapping(value = "authenticate", produces = "application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public AjaxResult authenticate(@RequestBody JSONObject jsonParams) {
        String code = jsonParams.getStr("code");
        if (code == null || code.equals("")) {
            return error(402, "code不能为空");
        }
        String backendToken = getBackendToken();
        if (backendToken.equals("")) {
            return error(402, "backendToken获取失败");
        }
        // 构建请求参数
        Map<String, String> params = new HashMap<String, String>(0);
        params.put("code", code);
        params.put("appId", UnionpayConfig.getAppId());
        params.put("backendToken", backendToken);
        params.put("grantType", "authorization_code");
        //发送请求
        //发送请求
        JSONObject result = callApi(UnionpayConfig.getGetAccessTokenUrl(), params);
        if (result == null) {
            return error(402, "接口获取失败");
        }
        return retobject(200, result.getStr("openId"));
//        String accessToken = result.getStr("accessToken");
//        String openId = result.getStr("openId");
//        Long expiresIn = result.getLong("expiresIn");

//        BaseEmp baseEmp = baseEmpService.selectByOpenId(openId);
//        int b;
//        if (baseEmp != null) {
//            baseEmp.setUpdateDate(new Date());
//            baseEmp.setAccessToken(accessToken);
//            baseEmp.setExpiredAt(System.currentTimeMillis() + expiresIn * 1000);
//            b = baseEmpService.updateByPrimaryKeySelective(baseEmp);
//        } else {
//            baseEmp = new BaseEmp();
//            baseEmp.setId(SnowflakeIdWorker.getUUID());
//            baseEmp.setUpdateDate(new Date());
//            baseEmp.setCreateDate(new Date());
//            baseEmp.setAccessToken(accessToken);
//            baseEmp.setOpenId(openId);
//            baseEmp.setExpiredAt(System.currentTimeMillis() + expiresIn * 1000);
//            b = baseEmpService.insertSelective(baseEmp);
//        }
//
//        if (b <=0) {
//            return error(402, "存储失败");
//        }
//	    return retobject(200, baseEmp.getOpenId());
    }

    /*给前端加密*/
    @PostMapping(value = "encryptForFront", produces = "application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public AjaxResult encryptForFront(@RequestBody JSONObject jsonParams) {
        Map<String, String> params = new HashMap<String, String>(0);
        params.put("url", jsonParams.getStr("url"));
        params.put("appId", UnionpayConfig.getAppId());
        if (UnionpayConfig.getFrontToken() == null || UnionpayConfig.getFrontTokenExprireTime().before(new Date())) {
            if (!createFrontToken()) {
                return error(402, "获取失败");
            }
        }
        String frontToken = UnionpayConfig.getFrontToken();
        params.put("frontToken", frontToken);
        //生成随即字符串
        String nonceStr = UnionpayUtils.createNonceStr();
        params.put("nonceStr", nonceStr);
        //生成时间戳
        String timestamp = String.valueOf(System.currentTimeMillis() / 1000);
        params.put("timestamp", timestamp);
        //签名 (appId,secret,nonceStr,timestamp)
        String value = UnionpayUtils.sortMap(params);
        String signature = UnionpayUtils.sha256(value.getBytes());
        params.put("signature", signature);
        return retobject(200, params);
    }

    /*行业卡操作*/
    private Boolean putIndustryCard(String openId, String cardNo, String transTp, String name, String orgCode, BaseEmp baseEmp) {

        Map<String, String> params = new HashMap<String, String>(0);
        params.put("appId", UnionpayConfig.getAppId());
        params.put("backendToken", getBackendToken());
        params.put("cardModuleId", UnionpayConfig.getCardModuleId());
        params.put("bussCardNo", cardNo);
        params.put("cardSubTp", UnionpayConfig.getCardSubTp());
        params.put("transTp", transTp);
        params.put("openId", openId);
        params.put("region", "");
        params.put("channelNo", "");
        params.put("name", "");
        params.put("certId", "");
        params.put("mobileNo", "");

        JSONObject resp = callApi(UnionpayConfig.getPutIndustryCardUrl(), params);
        if (resp == null) {
            return false;
        }

        if (transTp.equals("01")) {
            baseEmp.setUpdateDate(new Date());
            baseEmp.setEmpName(name);
            baseEmp.setCardNo(cardNo);
            baseEmp.setOpenId(openId);
            baseEmp.setOrgCode(orgCode);
            baseEmp.setEncryptCardNo(MD5Util.encode(cardNo));
            baseEmp.setStatus(1);
            baseEmpService.updateByPrimaryKeySelective(baseEmp);
        } else if (transTp.equals("03")) {
            baseEmp.setUpdateDate(new Date());
            baseEmp.setStatus(0);
            baseEmpService.updateByPrimaryKeySelective(baseEmp);
        }
        return true;
    }

    @PostMapping(value = "auth", produces = "application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public AjaxResult auth(@RequestBody JSONObject params) {
        String empName = params.getStr("empName");
        String cardNo = params.getStr("cardNo");
        String orgCode = params.getStr("orgCode");
        String openId = params.getStr("openId");
        if (StringUtils.isEmpty(empName)) {
            return error(402, "姓名不能为空");
        }
        if (StringUtils.isEmpty(cardNo)) {
            return error(402, "卡号不能为空");
        }
        if (StringUtils.isEmpty(orgCode)) {
            return error(402, "机构号不能为空");
        }
        if (StringUtils.isEmpty(openId)) {
            return error(402, "openId不能为空");
        }

        BaseEmp baseEmp = baseEmpService.selectByCardNo(cardNo);

        if (baseEmp == null) {
            return error(402, "用户不存在");
        }
        // 先调用用户校验接口
        JSONObject resp = validateUser(empName, cardNo);

        if (resp == null) {
            return error(402, "用户校验失败");
        }
        baseEmp.setDeptName(resp.getStr("deptName"));
        baseEmp.setEmpMobile(resp.getStr("phone"));

        if (!putIndustryCard(openId, cardNo, "01", empName, orgCode, baseEmp)) {
            return error(402, "操作失败");
        }

        return retobject(200, "成功");
    }

    @PostMapping(value = "unbind", produces = "application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public AjaxResult unbind(@RequestBody JSONObject params) {
        String cardNo = params.getStr("cardNo");
        if (StringUtils.isEmpty(cardNo)) {
            return error(402, "卡号不能为空");
        }

        BaseEmp baseEmp = baseEmpService.selectByCardNo(cardNo);

        if (baseEmp == null) {
            return error(402, "用户不存在");
        }

        if (!putIndustryCard(baseEmp.getOpenId(), cardNo, "03", baseEmp.getEmpName(), baseEmp.getOrgCode(), baseEmp)) {
            return error(402, "操作失败");
        }

        return retobject(200, "成功");
    }

    @PostMapping(value = "getEmp", produces = "application/json;charset=utf-8")
    @ResponseBody
//    @ApiTokenValid
    public AjaxResult getEmp(@RequestBody JSONObject params) {
        String encryptCardNo = params.getStr("encryptCardNo");
        if (StringUtils.isEmpty(encryptCardNo)) {
            return error(402, "encryptCardNo不能为空");
        }

        BaseEmp baseEmp = baseEmpService.selectByEncryptCardNo(encryptCardNo);

        if (baseEmp == null) {
            return error(402, "用户不存在");
        }
        return retobject(200, baseEmp);
    }

    private Map<String, String> getAllParams(Map<String, String> bizContent, String svcApi) {
        Map<String, String> params = new HashMap<>(0);
        // 公共必填参数
        params.put("version", "1.0.0");
        params.put("timestamp", DateUtils.format(new Date(), "YYYYMMDDhhmmss"));
        params.put("signType", "RSA2");
        params.put("svcApi", svcApi);
//        params.put("svcId", "201911190952180000000012041674");
        params.put("svcId", "20200222123422");
        bizContent.put("version", "1.0");
        Gson gson = new Gson();
        String bizContentStr = "";
        try {
            bizContentStr = gson.toJson(bizContent);
        } catch (Exception e) {
            logger.info(e.getMessage());
        }
        params.put("bizContent", bizContentStr);
        // 非必填参数
        params.put("charset", "utf-8");
        params.put("format", "json");

        //签名
        String signature;

        try {
            signature = UnionpayUtils.signForUnionpay(params, UnionpayUtils.getPrivateKey());
        } catch (Exception e) {
            logger.info("签名失败" + params);
            return null;
        }
        params.put("sign", signature);

        return params;
    }

    /*
     * 调用银联食堂接口
     * */
    private JSONObject callUnionpayApi(Map<String, String> params) {
        String resultStr;
        try {
            logger.info("接口调用参数：" + params);
            resultStr = UnionpayUtils.sendPostGson(UnionpayConfig.getValidateUserUrl(), params);
            logger.info("接口调用返回：" + resultStr);
        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }

        JSONObject jsonObj = new JSONObject(resultStr);
        if (jsonObj.get("respCd") != null) {
            return null;
        }

        JSONObject resp = new JSONObject(jsonObj.getStr("respContent"));
        if (!"1000".equals(resp.getStr("code"))) {
            return null;
        }

        return resp;
    }

    /**
     * 用户验证接口
     * @param name
     * @param cardNo
     * @return cn.hutool.json.JSONObject
     * @author valley
     * @date 2020/2/26 12:36
     */
    private JSONObject validateUser(String name, String cardNo) {
        Map<String, String> bizContent = new HashMap<>(0);
        bizContent.put("name", name);
        bizContent.put("cardNo", cardNo);
        bizContent.put("sysId", "A349AG8LA1");
        bizContent.put("phone", "");
        // 2.对公共参数进行签名
        Map<String, String> params = getAllParams(bizContent, "up.fpsd.trade.houqin.user.check");
        // 3.返送请求接收返回参数
        return callUnionpayApi(params);
    }

    /**
     * 余额查询接口
     * @param jsonParams
     * @return com.cpdms.common.domain.AjaxResult
     * @author valley
     * @date 2020/2/26 12:37
     */
    @PostMapping(value = "queryAccount", produces = "application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public AjaxResult queryAccount(@RequestBody JSONObject jsonParams) {
        if (jsonParams.get("cardNo") == null) {
            return error(402, "卡号不能为空");
        }
        String cardNo = jsonParams.getStr("cardNo");
        Map<String, String> bizContent = new HashMap<>(0);
        bizContent.put("cardNo", cardNo);
        bizContent.put("sysId", "A349AG8LA1");
        bizContent.put("phoneNo", "");
        // 2.对公共参数进行签名
        Map<String, String> params = getAllParams(bizContent, "up.fpsd.trade.houqin.balance.query");
        // 3.返送请求接收返回参数
        JSONObject resp = callUnionpayApi(params);
        if (resp == null) {
            return error(402, "余额查询失败");
        }

        return retobject(200, resp);
    }

    /**
     * 订单支付接口
     * @param jsonParams
     * @return com.cpdms.common.domain.AjaxResult
     * @author valley
     * @date 2020/2/26 12:37
     */
    @PostMapping(value = "/pay", produces = "application/json;charset=utf-8")
    @ResponseBody
    @Transactional
    @ApiTokenValid
    public AjaxResult pay(@RequestBody JSONObject jsonParams) {
        if (jsonParams.get("id") == null || jsonParams.get("amt") == null || jsonParams.get("cardNo") == null) {
            return error(402, "订单ID或者支付金额不能为空");
        }
        String id = jsonParams.getStr("id");
        String cardNo = jsonParams.getStr("cardNo");
        BigDecimal amt = jsonParams.getBigDecimal("amt");
        BizOrder bizOrder = bizOrderService.selectByPrimaryKey(id);
        if (bizOrder == null) {
            return error(402, "订单不存在");
        }
        if (!bizOrder.getSellTypeName().equals("外卖")) {
            return error(402, "订单类型错误");
        }
        if (!bizOrder.getOrdState().equals(Constants.DINNING_ORD_ORDER)) {
            return error(402, "订单状态错误");
        }
        if (bizOrder.getOrdAmt().compareTo(amt) != 0) {
            return error(402, "订单金额错误");
        }
        if (!bizOrder.getCardNo().equals(cardNo)) {
            return error(402, "卡号错误");
        }
        // 调用支付
        Date transDtTm = new Date();
        Map<String, String> bizContent = new HashMap<>(0);
        bizContent.put("cardNo", cardNo);
        bizContent.put("transDtTm", DateUtils.format(transDtTm, "YYYYMMDDHHMMSS"));
        bizContent.put("orderId", bizOrder.getOrdCode());
        BigDecimal transAmt = amt.multiply(new BigDecimal(100));
        bizContent.put("transAmt", transAmt.toString());
        bizContent.put("transType", "03");
        bizContent.put("sysId", "A349AG8LA1");
        bizContent.put("devCode", "123456");

        Map<String, String> params = getAllParams(bizContent, "up.fpsd.trade.houqin.pay");
        JSONObject resp = callUnionpayApi(params);
        if (resp == null) {
            return error(402, "支付失败");
        }

        BizOrdPay bizOrdPay = new BizOrdPay(SnowflakeIdWorker.getUUID(), id, bizOrder.getOrdCode(), amt, bizOrder.getOrdCode(), "online", cardNo, transDtTm, "", new Date());
        bizOrdPayService.insertSelective(bizOrdPay);
        BizPayment bizPayment = new BizPayment(SnowflakeIdWorker.getUUID(), amt, bizOrder.getOrdCode(), "online", 0, cardNo, transDtTm, "", new Date());
        bizPaymentService.insertSelective(bizPayment);
        bizOrder.setOrdState(Constants.DINNING_ORD_PAYED);
        bizOrder.setUpdateDate(new Date());
        bizOrderService.updateByPrimaryKeySelective(bizOrder);

        return retobject(200, "支付成功");
    }

    /**
     * 冲正接口
     * @param jsonParams
     * @return com.cpdms.common.domain.AjaxResult
     * @author valley
     * @date 2020/2/26 12:37
     */
    @PostMapping(value = "/refund", produces = "application/json;charset=utf-8")
    @ResponseBody
    @Transactional
    @ApiTokenValid
    public AjaxResult refund(@RequestBody JSONObject jsonParams) {
        if (jsonParams.get("id") == null || jsonParams.get("cardNo") == null) {
            return error(402, "订单ID不能为空");
        }
        String id = jsonParams.getStr("id");
        String cardNo = jsonParams.getStr("cardNo");
        BizOrder bizOrder = bizOrderService.selectByPrimaryKey(id);
        if (bizOrder == null) {
            return error(402, "订单不存在");
        }
        if (!bizOrder.getSellTypeName().equals("外卖")) {
            return error(402, "订单类型错误");
        }
        if (!bizOrder.getOrdState().equals(Constants.DINNING_ORD_PAYED)) {
            return error(402, "订单状态错误");
        }
        if (!bizOrder.getCardNo().equals(cardNo)) {
            return error(402, "卡号错误");
        }

        BizOrdPay bizOrdPay = bizOrdPayService.selectByOrdId(id);
        if (bizOrdPay == null) {
            return error(402, "订单错误");
        }
        // 调用支付
        Date transDtTm = new Date();
        Map<String, String> bizContent = new HashMap<>(0);
        bizContent.put("cardNo", cardNo);
        bizContent.put("transDtTm", DateUtils.format(transDtTm, "YYYYMMDDHHMMSS"));
        bizContent.put("origTransDtTm", DateUtils.format(bizOrdPay.getPayDate(), "YYYYMMDDHHMMSS"));
        bizContent.put("orderId", bizOrder.getOrdCode());
        bizContent.put("sysId", "A349AG8LA1");
        bizContent.put("devCode", "");

        Map<String, String> params = getAllParams(bizContent, "up.fpsd.trade.houqin.reversal");
        JSONObject resp = callUnionpayApi(params);
        if (resp == null) {
            return error(402, "冲正失败");
        }
        BizPayment bizPayment = new BizPayment(SnowflakeIdWorker.getUUID(), bizOrder.getOrdAmt(), bizOrdPay.getPayTransId(), "online", 2, cardNo, transDtTm, "", new Date());
        bizPaymentService.insertSelective(bizPayment);
        bizOrder.setOrdState(Constants.DINNING_ORD_CANCEL);
        bizOrder.setUpdateDate(new Date());
        bizOrderService.updateByPrimaryKeySelective(bizOrder);

        return retobject(200, "冲正成功");
    }

    /**
     * 充值接口
     * @param jsonParams
     * @return com.cpdms.common.domain.AjaxResult
     * @author valley
     * @date 2020/2/26 12:39
     */
    @PostMapping(value = "/recharge", produces = "application/json;charset=utf-8")
    @ResponseBody
    @Transactional
    @ApiTokenValid
    public AjaxResult recharge(@RequestBody JSONObject jsonParams) {
        if (jsonParams.get("amt") == null || jsonParams.get("cardNo") == null) {
            return error(402, "卡号或者充值金额不能为空");
        }
        String cardNo = jsonParams.getStr("cardNo");
        BigDecimal amt = jsonParams.getBigDecimal("amt");
        BaseEmp baseEmp = baseEmpService.selectByCardNo(cardNo);
        if (baseEmp == null) {
            return error(402, "卡号错误");
        }
        // 调用支付
        Date transDtTm = new Date();
        String orderId = SnowflakeIdWorker.getUUID();
        Map<String, String> bizContent = new HashMap<>(0);
        bizContent.put("cardNo", cardNo);
        bizContent.put("transDtTm", DateUtils.format(transDtTm, "YYYYMMDDHHMMSS"));
        bizContent.put("orderId", orderId);
        BigDecimal transAmt = amt.multiply(new BigDecimal(100));
        bizContent.put("transAmt", transAmt.toString());
        bizContent.put("sysId", "A349AG8LA1");

        Map<String, String> params = getAllParams(bizContent, "up.fpsd.trade.houqin.balance.recharge");
        JSONObject resp = callUnionpayApi(params);
        if (resp == null) {
            return error(402, "充值失败");
        }

        BizPayment bizPayment = new BizPayment(SnowflakeIdWorker.getUUID(), amt, orderId, "online", 1, cardNo, transDtTm, "", new Date());
        bizPaymentService.insertSelective(bizPayment);

        return retobject(200, "充值成功");
    }

    /*
     * @description: 获取所有机构信息
     * @param void
     * @return: com.cpdms.common.domain.AjaxResult
     * @author: Zhipeng Ge
     * @time: 2020/3/12 16:44
     */  
    @GetMapping(value = "/getDeptList", produces = "application/json;charset=utf-8")
    @ResponseBody
    public AjaxResult getDeptList()
    {
        Map<String, Object> map = new HashMap<>();
        List<TSysDept> deptList = sysDeptService.queryDeptList(map);

        return retobject(200, deptList);
    }
}
