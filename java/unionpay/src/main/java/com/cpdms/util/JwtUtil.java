package com.cpdms.util;

import cn.hutool.log.Log;
import cn.hutool.log.LogFactory;
import com.auth0.jwt.JWT;
import com.auth0.jwt.JWTVerifier;
import com.auth0.jwt.algorithms.Algorithm;
import com.auth0.jwt.exceptions.JWTDecodeException;
import com.auth0.jwt.interfaces.DecodedJWT;
import com.cpdms.common.conf.EncryptConf;
import com.cpdms.common.token.TokenConstant;
import jdk.nashorn.internal.parser.Token;
import org.springframework.stereotype.Component;

import java.util.Date;

@Component
public class JwtUtil {
    private static final Log logger = LogFactory.get(JwtUtil.class);
    /**
     * 校验token是否正确
     *
     * @param token
     *            Token
     * @return boolean 是否正确
     */
    public static boolean verify(String token) {
        try {
            // 帐号加JWT私钥解密
            String secret = getClaim(token,"account") + AESUtils.aesDecrypt(EncryptConf.getEncryptJWTKey());
            Algorithm algorithm = Algorithm.HMAC256(secret);
            JWTVerifier verifier = JWT.require(algorithm).build();
            DecodedJWT jwt = verifier.verify(token);
            return true;
        } catch (Exception e) {
            logger.error("JWTToken认证解密出现UnsupportedEncodingException异常:" + e.getMessage());
            throw new RuntimeException("JWTToken认证解密出现UnsupportedEncodingException异常:" + e.getMessage());
        }
    }

    /**
     * 获得Token中的信息无需secret解密也能获得
     *
     * @param token
     * @param claim
     * @return java.lang.String
     */
    public static String getClaim(String token, String claim) {
        try {
            DecodedJWT jwt = JWT.decode(token);
            // 只能输出String类型，如果是其他类型返回null
            return jwt.getClaim(claim).asString();
        } catch (JWTDecodeException e) {
            logger.error("解密Token中的公共信息出现JWTDecodeException异常:" + e.getMessage());
            throw new RuntimeException("解密Token中的公共信息出现JWTDecodeException异常:" + e.getMessage());
        }
    }

    /**
     * 生成签名
     *
     * @param account
     *            帐号
     * @return java.lang.String 返回加密的Token
     */
    public static String sign(String account, String currentTimeMillis) {
        try {
            // 帐号加JWT私钥加密

            String secret = account + AESUtils.aesEncrypt(EncryptConf.getEncryptJWTKey());
            // 此处过期时间是以毫秒为单位，所以乘以1000
            Date date = new Date(System.currentTimeMillis() + (EncryptConf.getAccessTokenExpireTime()) * 1000);
            Algorithm algorithm = Algorithm.HMAC256(secret);
            // 附带account帐号信息
            return JWT.create().withClaim(TokenConstant.TOKEN_ACCOUNT, account).withClaim(TokenConstant.CURRENT_TIME_MILLIS, currentTimeMillis)
                    .withExpiresAt(date).sign(algorithm);
        } catch (Exception e) {
            logger.error("JWTToken加密出现UnsupportedEncodingException异常:" + e.getMessage());
            throw new RuntimeException("JWTToken加密出现UnsupportedEncodingException异常:" + e.getMessage());
        }
    }


}
