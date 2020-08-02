package com.cpdms.common.conf;

import com.cpdms.common.interceptor.MybatisDataAuthInterceptor;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

@Configuration
public class MybatisConfiguration {
@Bean
    MybatisDataAuthInterceptor mybatisDataAuthInterceptor(){
        return new MybatisDataAuthInterceptor();
    }
}
