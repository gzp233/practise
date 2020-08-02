package com.cpdms.common.interceptor;

import com.cpdms.model.auto.TsysUser;
import com.cpdms.shiro.util.ShiroUtils;
import org.apache.ibatis.executor.Executor;
import org.apache.ibatis.mapping.MappedStatement;
import org.apache.ibatis.mapping.SqlCommandType;
import org.apache.ibatis.plugin.*;
import org.springframework.stereotype.Component;

import java.lang.reflect.Field;
import java.util.Arrays;
import java.util.List;
import java.util.Properties;

/**
 * @description:判断一下新增语句如果有deptId字段，就自动设置
 * @author: Zhipeng Ge
 * @time: 2020/3/11 15:57
 */
@Component
@Intercepts({@Signature(method = "update", type = Executor.class, args = {MappedStatement.class, Object.class})})
public class AddDeptIdInterceptor implements Interceptor {
    private static final List<String> deptIds = Arrays.asList(
            "deptId","aDeptId","bcDeptId","beDeptId","bfDeptId","bhDeptId","bhsDeptId","bhDeptId","bpDeptId","bstDeptId",
            "btrDeptId","bvDeptId","beoDeptId","bhoDeptId","bofDeptId","bopDeptId","boDeptId","ddDeptId","fnDeptId"
    );

    @Override
    public Object intercept(Invocation invocation) throws Throwable {
        MappedStatement mappedStatement = (MappedStatement)invocation.getArgs()[0];

        // 获取 SQL
        SqlCommandType sqlCommandType = mappedStatement.getSqlCommandType();

        // 获取参数
        Object parameter = invocation.getArgs()[1];

        // 获取私有成员变量
        Field[] declaredFields = parameter.getClass().getDeclaredFields();

        for (Field field : declaredFields)
        {
            String fieldName = field.getName();
            if (deptIds.contains(fieldName)) {
                // insert语句插入deptId
                field.setAccessible(true);
                if (field.get(parameter) == null && SqlCommandType.INSERT.equals(sqlCommandType)) {
                    TsysUser user = ShiroUtils.getUser();
                    field.set(parameter, user.getDeptId());
                }
            }
        }

        return invocation.proceed();
    }

    @Override
    public Object plugin(Object o) {
        return Plugin.wrap(o, this);
    }

    @Override
    public void setProperties(Properties properties) {

    }
}
