package com.cpdms.common.filter;

import cn.hutool.log.Log;
import cn.hutool.log.LogFactory;
import org.apache.commons.lang.StringUtils;

/**
 * SQL过滤
 *
 * @author Mark sunlightcs@gmail.com
 */
public class SQLFilter {
private static    Log logger = LogFactory.get(SQLFilter.class);
    /**
     * SQL注入过滤
     * @param str  待验证的字符串
     */
    public static String sqlInject(String str) {
        if(StringUtils.isBlank(str)){
            return null;
        }
        //去掉'|"|;|\字符
        String instr = str;
        instr = StringUtils.replace(instr, "'", "");
        instr = StringUtils.replace(instr, "\"", "");
        instr = StringUtils.replace(instr, ";", "");
        instr = StringUtils.replace(instr, "\\", "");

               //非法字符
        String[] keywords = {"master", "truncate", "insert", "select", "delete", "update", "declare", "alter", "drop"," and "," or "," * ","count",";","exec ","%"};

        //判断是否包含非法字符
        for(String keyword : keywords){
           // logger.info("indix of " + instr.indexOf(keyword));
            if(instr.indexOf(keyword) != -1){
                instr =  StringUtils.replace(instr,keyword," ");
                logger.info("SQL Injection Filter from " + str +" to " + instr) ;
            }
        }

        return instr;
    }
}