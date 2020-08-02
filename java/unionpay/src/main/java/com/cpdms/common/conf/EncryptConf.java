package com.cpdms.common.conf;

import org.springframework.boot.context.properties.ConfigurationProperties;
import org.springframework.stereotype.Component;

@Component
@ConfigurationProperties(prefix = "encryption")
public class EncryptConf {
    public static String getEncryptAESKey() {
        return encryptAESKey;
    }

    public static void setEncryptAESKey(String encryptAESKey) {
        EncryptConf.encryptAESKey = encryptAESKey;
    }

    public static String getEncryptJWTKey() {
        return encryptJWTKey;
    }

    public static void setEncryptJWTKey(String encryptJWTKey) {
        EncryptConf.encryptJWTKey = encryptJWTKey;
    }

    public static Long getAccessTokenExpireTime() {
        return accessTokenExpireTime;
    }

    public static void setAccessTokenExpireTime(Long accessTokenExpireTime) {
        EncryptConf.accessTokenExpireTime = accessTokenExpireTime;
    }



    public static String getRefreshTokenExpireTime() {
        return refreshTokenExpireTime;
    }

    public static void setRefreshTokenExpireTime(String refreshTokenExpireTime) {
        EncryptConf.refreshTokenExpireTime = refreshTokenExpireTime;
    }

    private static String encryptAESKey;
    private static String encryptJWTKey;
    private static Long accessTokenExpireTime;
    private static String refreshTokenExpireTime;


}
