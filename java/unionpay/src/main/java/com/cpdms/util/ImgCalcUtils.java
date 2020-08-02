package com.cpdms.util;

import com.sun.jna.*;
import com.sun.jna.ptr.IntByReference;
import com.sun.jna.ptr.PointerByReference;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.util.ClassUtils;

import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.InputStreamReader;

public class ImgCalcUtils {
    private static Logger logger = LoggerFactory.getLogger(ImgCalcUtils.class);

    public interface ImgCalcSo extends Library {
        ImgCalcSo INSTANCE = (ImgCalcSo) Native.load("cwinterface", ImgCalcSo.class);
        int LEN_FEA_VER = 10;
        int LEN_FEAT_FC_CW = 2056*2;
        int face_cw_det_init(PointerByReference pDet, String cfgPath);
        int face_cw_feature_get_init(PointerByReference pFea, String cfgPath);
        int face_cw_get_feature(String picNm, String feature, IntByReference fLen, Pointer pDet, Pointer pFea, String feaVer);
        int face_cw_det_free(PointerByReference pDet);
        int face_cw_feature_get_free(PointerByReference pFea);

    }

    public static String calcByGo(String imgPath) {
        String result = "";
        String jpgPath= ClassUtils.getDefaultClassLoader().getResource("").getPath() + imgPath;
        try {
            String cmds[] = new String[3];
            cmds[0] = "/root/FaceFeature/feature";
            cmds[1] = "-f";
            cmds[2] = jpgPath;// 图片名称
            // 执行go脚本
            Process pcs = Runtime.getRuntime().exec(cmds);
            // 定义返回值
            BufferedInputStream in = new BufferedInputStream(pcs.getInputStream());
            BufferedReader br = new BufferedReader(new InputStreamReader(in));
            String lineStr;
            while ((lineStr = br.readLine()) != null) {
                result = lineStr;
            }
            // 关闭输入流
            br.close();
            in.close();
        } catch (Exception e) {
            e.printStackTrace();
        }

        return result;
    }

    public static String getDet(String imgPath) {
        System.setProperty("jna.library.path","/lib");
        String jpgPath= ClassUtils.getDefaultClassLoader().getResource("").getPath() + imgPath;
//        String jpgPath= "/usr/local/tomcat/webapps/ROOT/WEB-INF/classes/static/personnel/dec6e58f072eb14eba6ca9933480b2a9.jpg";
        PointerByReference pDet = new PointerByReference();
        PointerByReference pFea = new PointerByReference();
        int ret = ImgCalcSo.INSTANCE.face_cw_det_init(pDet, "/root/FaceFeature/cloudwalk/etc/detect_config.cfg");
        if (ret < 0) {
            return "";
        }
        ret = ImgCalcSo.INSTANCE.face_cw_feature_get_init(pFea, "/root/FaceFeature/cloudwalk/etc/feaget_config.cfg");
        if (ret < 0) {
            return "";
        }
        IntByReference fLen = new IntByReference(0);
        String feature = "";
        for (int i = 0;i < 4113;i++) {
            feature += " ";
        }
        String feaVer = "";
        for (int i = 0;i < 11;i++) {
            feaVer += " ";
        }
        logger.info("图片路径:" + jpgPath);
        ret = ImgCalcSo.INSTANCE.face_cw_get_feature(jpgPath, feature, fLen, pDet.getPointer(), pFea.getPointer(), feaVer);
        logger.info("ret的值为:" + ret);
        logger.info("特征值为:" + feature.toString());
        if (ret < 0) {
            return "";
        }
        return "ok";
    }
}
