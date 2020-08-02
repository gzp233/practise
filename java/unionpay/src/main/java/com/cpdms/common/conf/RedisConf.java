package com.cpdms.common.conf;

import org.springframework.beans.factory.annotation.Value;
import org.springframework.boot.context.properties.ConfigurationProperties;
import org.springframework.context.annotation.Configuration;
import org.springframework.stereotype.Component;

@Component
@ConfigurationProperties(prefix = "spring.redis")
public class RedisConf {
   // @Value("${redis.host}")
    private static String host;
  //  @Value("${redis.port}")
    private static Integer port;
   // @Value("${redis.timeout}")
    private static String timeout;

    public static String getHost() {
        return host;
    }

    public static void setHost(String host) {
        RedisConf.host = host;
    }

    public static Integer getPort() {
        return port;
    }

    public static void setPort(Integer port) {
        RedisConf.port = port;
    }

    public static String getTimeout() {
        return timeout;
    }

    public static void setTimeout(String timeout) {
        RedisConf.timeout = timeout;
    }

    public static String getDatabase() {
        return database;
    }

    public static void setDatabase(String database) {
        RedisConf.database = database;
    }

    public static boolean getIsRedisSession() {
        return isRedisSession;
    }

    public static void setIsRedisSession(boolean isRedisSession) {
        RedisConf.isRedisSession = isRedisSession;
    }

    public static Integer getExpiretime() {
        return expiretime;
    }

    public static void setExpiretime(Integer expiretime) {
        RedisConf.expiretime = expiretime;
    }

    //  @Value("${redis.database}")
    private static  String  database;

  //  @Value("${redis.isRedisSession}")
    private static boolean isRedisSession;
 //   @Value("${redis.expiretime}")
    private static Integer expiretime;



}
