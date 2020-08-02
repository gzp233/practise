package com.cpdms.common.conf;

import org.springframework.boot.context.properties.ConfigurationProperties;
import org.springframework.stereotype.Component;

import java.util.Date;

/**
 * 读取云闪付配置
 * @author zhangxin
 *
 */
@Component
@ConfigurationProperties(prefix = "unionpay")
public class UnionpayConfig {
	/** appId **/
	private static String appId;
	/** secret **/
	private static String secret;
	/** getBackendTokenUrl **/
	private static String getBackendTokenUrl;
	/** BackendToken **/
	private static String backendToken;
	/** BackendTokenExpireTime **/
	private static Date backendTokenExprireTime;
	/** getBackendTokenUrl **/
	private static String getFrontTokenUrl;
	/** BackendToken **/
	private static String frontToken;
	/** BackendTokenExpireTime **/
	private static Date frontTokenExprireTime;
	/** getAccessTokenUrl **/
	private static String getAccessTokenUrl;
	/*行业卡操作*/
	private static String putIndustryCardUrl;
	/*行业卡Id*/
    private static String cardModuleId;
    /*行业卡子类型*/
    private static String cardSubTp;
    /*获取code的回调地址*/
    private static String backUrl;
    /*行员信息同步接口*/
    private static String userSynUrl;
    /*行员关闭接口*/
    private static String closeSerUrl;

    public static String getCloseSerUrl() {
        return closeSerUrl;
    }

    public static void setCloseSerUrl(String closeSerUrl) {
        UnionpayConfig.closeSerUrl = closeSerUrl;
    }

    public static String getUserSynUrl() {
        return userSynUrl;
    }

    public static void setUserSynUrl(String userSynUrl) {
        UnionpayConfig.userSynUrl = userSynUrl;
    }

    private static String validateUserUrl;

    public static String getValidateUserUrl() {
        return validateUserUrl;
    }

    public static void setValidateUserUrl(String validateUserUrl) {
        UnionpayConfig.validateUserUrl = validateUserUrl;
    }

    public static String getBackUrl() {
        return backUrl;
    }

    public static void setBackUrl(String backUrl) {
        UnionpayConfig.backUrl = backUrl;
    }

    public static String getCardModuleId() {
        return cardModuleId;
    }

    public static void setCardModuleId(String cardModuleId) {
        UnionpayConfig.cardModuleId = cardModuleId;
    }

    public static String getCardSubTp() {
        return cardSubTp;
    }

    public static void setCardSubTp(String cardSubTp) {
        UnionpayConfig.cardSubTp = cardSubTp;
    }

    public static String getPutIndustryCardUrl() {
        return putIndustryCardUrl;
    }

    public static void setPutIndustryCardUrl(String putIndustryCardUrl) {
        UnionpayConfig.putIndustryCardUrl = putIndustryCardUrl;
    }

    public static String getGetFrontTokenUrl() {
        return getFrontTokenUrl;
    }

    public static void setGetFrontTokenUrl(String getFrontTokenUrl) {
        UnionpayConfig.getFrontTokenUrl = getFrontTokenUrl;
    }

    public static String getFrontToken() {
        return frontToken;
    }

    public static void setFrontToken(String frontToken) {
        UnionpayConfig.frontToken = frontToken;
    }

    public static Date getFrontTokenExprireTime() {
        return frontTokenExprireTime;
    }

    public static void setFrontTokenExprireTime(Date frontTokenExprireTime) {
        UnionpayConfig.frontTokenExprireTime = frontTokenExprireTime;
    }

    public static String getGetAccessTokenUrl() {
        return getAccessTokenUrl;
    }

    public static void setGetAccessTokenUrl(String getAccessTokenUrl) {
        UnionpayConfig.getAccessTokenUrl = getAccessTokenUrl;
    }

    public static String getBackendToken() {
        return backendToken;
    }

    public static void setBackendToken(String backendToken) {
        UnionpayConfig.backendToken = backendToken;
    }

    public static Date getBackendTokenExprireTime() {
        return backendTokenExprireTime;
    }

    public static void setBackendTokenExprireTime(Date backendTokenExprireTime) {
        UnionpayConfig.backendTokenExprireTime = backendTokenExprireTime;
    }

    public static String getAppId() {
		return appId;
	}
	public static void setAppId(String appId) {
		UnionpayConfig.appId = appId;
	}
	public static String getSecret() {
		return secret;
	}
	public static void setSecret(String secret) {
		UnionpayConfig.secret = secret;
	}
	public static String getGetBackendTokenUrl() {
		return getBackendTokenUrl;
	}
	public static void setGetBackendTokenUrl(String getBackendTokenUrl) {
		UnionpayConfig.getBackendTokenUrl = getBackendTokenUrl;
	}
}
