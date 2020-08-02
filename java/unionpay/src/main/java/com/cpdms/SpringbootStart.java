package com.cpdms;

import org.mybatis.spring.annotation.MapperScan;
import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.scheduling.annotation.EnableAsync;


/**
 * 项目启动方法
 * @author fuce
 *
 */
@EnableAsync
@SpringBootApplication
@MapperScan(value = "com.cpdms.mapper")
public class SpringbootStart {

    public static void main(String[] args) {

        SpringApplication.run(SpringbootStart.class, args);
        System.out.println("***********Start Successfully*****************\n");
    }
}
