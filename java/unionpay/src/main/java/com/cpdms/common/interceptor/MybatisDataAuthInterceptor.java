package com.cpdms.common.interceptor;

import cn.hutool.log.Log;
import cn.hutool.log.LogFactory;
import com.cpdms.common.annotation.DataFilter;
import com.cpdms.model.auto.TsysUser;
import com.cpdms.service.SysDeptService;
import com.cpdms.shiro.util.ShiroUtils;
import net.sf.jsqlparser.expression.Expression;
import net.sf.jsqlparser.expression.Parenthesis;
import net.sf.jsqlparser.expression.operators.conditional.AndExpression;
import net.sf.jsqlparser.parser.CCJSqlParserManager;
import net.sf.jsqlparser.parser.CCJSqlParserUtil;
import net.sf.jsqlparser.statement.select.PlainSelect;
import net.sf.jsqlparser.statement.select.Select;
import org.apache.commons.lang3.StringUtils;
import org.apache.ibatis.executor.Executor;
import org.apache.ibatis.mapping.BoundSql;
import org.apache.ibatis.mapping.MappedStatement;
import org.apache.ibatis.mapping.SqlCommandType;
import org.apache.ibatis.mapping.SqlSource;
import org.apache.ibatis.plugin.*;
import org.apache.ibatis.reflection.DefaultReflectorFactory;
import org.apache.ibatis.reflection.MetaObject;
import org.apache.ibatis.reflection.factory.DefaultObjectFactory;
import org.apache.ibatis.reflection.wrapper.DefaultObjectWrapperFactory;
import org.apache.ibatis.session.ResultHandler;
import org.apache.ibatis.session.RowBounds;
import org.springframework.beans.BeansException;
import org.springframework.context.ApplicationContext;
import org.springframework.context.ApplicationContextAware;


import javax.annotation.Resource;
import java.io.StringReader;
import java.lang.reflect.Method;
import java.util.*;

@Intercepts({@Signature(method = "query", type = Executor.class, args = {MappedStatement.class, Object.class, RowBounds.class, ResultHandler.class})})
public class MybatisDataAuthInterceptor implements Interceptor, ApplicationContextAware {
    private static ApplicationContext context;
    private final static Log logger = LogFactory.get(MybatisDataAuthInterceptor.class);
    @Resource
 SysDeptService sysDeptService;
    @Override
    public Object intercept(Invocation arg0) throws Throwable {
        MappedStatement mappedStatement = (MappedStatement) arg0.getArgs()[0];
        logger.info("进入拦截器");
        if (!SqlCommandType.SELECT.equals(mappedStatement.getSqlCommandType())) {
            return arg0.proceed();
        }
        Class<?> classType = Class.forName(mappedStatement.getId().substring(0, mappedStatement.getId().lastIndexOf(".")));
        String mName = mappedStatement.getId().substring(mappedStatement.getId().lastIndexOf(".") + 1, mappedStatement.getId().length());

        for (Method method : classType.getDeclaredMethods()) {
            if (method.isAnnotationPresent(DataFilter.class) && mName.equals(method.getName())) {
                /**
                 * 查找标识了该注解 的实现 类
                 */
                try {

/**
 *反射获取业务 sql
 */
                    DataFilter dataFilter = method.getAnnotation(DataFilter.class);
                    String whereSql ="";
                    TsysUser user = ShiroUtils.getUser();
                    if(null != user){
                        whereSql = getSQLFilter(user,dataFilter);
                    }
                   //  " dept_id = 1 ";/*(String) md.invoke(context.getBean(entry.getValue().getClass()), new Object[]{action.tableAlias()});*/
                    if (!StringUtils.isEmpty(whereSql) && !"null".equalsIgnoreCase(whereSql)) {
                        Object parameter = null;
                        if (arg0.getArgs().length > 1) {
                            parameter = arg0.getArgs()[1];
                        }
                        BoundSql boundSql = mappedStatement.getBoundSql(parameter);
                        MappedStatement newStatement = newMappedStatement(mappedStatement, new BoundSqlSqlSource(boundSql));

                        MetaObject msObject = MetaObject.forObject(newStatement, new DefaultObjectFactory(), new DefaultObjectWrapperFactory(), new DefaultReflectorFactory());
                            /** *
                             * 通过JSqlParser解析 原有sql,追加sql条件
                             */
                        logger.info("boundSql.getSql()" + boundSql.getSql());
                        CCJSqlParserManager parserManager = new CCJSqlParserManager();
                        Select select = (Select) parserManager.parse(new StringReader(boundSql.getSql()));
                        PlainSelect selectBody = (PlainSelect) select.getSelectBody();
                        Expression whereExpression = CCJSqlParserUtil.parseCondExpression(whereSql);
                        if(null != selectBody.getWhere()){
                            selectBody.setWhere(new AndExpression(selectBody.getWhere(), new Parenthesis(whereExpression)));
                        }else{
                            selectBody.setWhere(whereExpression);
                        }

                        /**
                         *修改sql
                         */
                        msObject.setValue("sqlSource.boundSql.sql", selectBody.toString());
                        arg0.getArgs()[0] = newStatement;
                        logger.info("Interceptor sql:" + selectBody.toString());
                    }
                } catch (Exception e) {
                    logger.error(null, e);
                }

            }

        }
        return arg0.proceed();
    }

    @Override
    public Object plugin(Object target) {
        if (target instanceof Executor) {
            return Plugin.wrap(target, this);
        }
        return target;
    }

    @Override
    public void setProperties(Properties properties) {

    }

    @Override
    public void setApplicationContext(ApplicationContext applicationContext) throws BeansException {
        context = applicationContext;
    }

    private MappedStatement newMappedStatement(MappedStatement ms,
                                               SqlSource newSqlSource) {
        MappedStatement.Builder builder = new MappedStatement.Builder(ms.getConfiguration(),
                ms.getId(), newSqlSource, ms.getSqlCommandType());
        builder.resource(ms.getResource());
        builder.fetchSize(ms.getFetchSize());
        builder.statementType(ms.getStatementType());
        builder.keyGenerator(ms.getKeyGenerator());
        if ((ms.getKeyProperties() != null) &&
                (ms.getKeyProperties().length != 0)) {
            StringBuilder keyProperties = new StringBuilder();
            for (String keyProperty : ms.getKeyProperties()) {
                keyProperties.append(keyProperty).append(",");
            }
            keyProperties.delete(keyProperties.length() - 1,
                    keyProperties.length());
            builder.keyProperty(keyProperties.toString());
        }
        builder.timeout(ms.getTimeout());
        builder.parameterMap(ms.getParameterMap());
        builder.resultMaps(ms.getResultMaps());
        builder.resultSetType(ms.getResultSetType());
        builder.cache(ms.getCache());
        builder.flushCacheRequired(ms.isFlushCacheRequired());
        builder.useCache(ms.isUseCache());
        return builder.build();
    }

    class BoundSqlSqlSource implements SqlSource {
        private BoundSql boundSql;

        public BoundSqlSqlSource(BoundSql boundSql) {
            this.boundSql = boundSql;
        }

        @Override
        public BoundSql getBoundSql(Object parameterObject) {
            return boundSql;
        }



    }

    /**
     * @param user
     * @param dataFilter
     * @return
     */
    private String getSQLFilter(TsysUser user, DataFilter dataFilter) {

        //获取表的别名
        String tableAlias = dataFilter.tableAlias();
        if (StringUtils.isNotBlank(tableAlias)) {
            tableAlias += ".";
        }

        //部门ID列表
        Set<String> deptIdList = new HashSet<>();
        if(StringUtils.isNotEmpty(user.getDeptId())){
            deptIdList.add(user.getDeptId());
        }

        //用户子部门ID列表
        if (dataFilter.subDept()) {
            List<String> subDeptIdList = sysDeptService.getSubDeptIdList(user.getDeptId());
            deptIdList.addAll(subDeptIdList);
        }

        StringBuilder sqlFilter = new StringBuilder();
        sqlFilter.append(" (");

        if (deptIdList.size() > 0) {
            sqlFilter.append(tableAlias).append(dataFilter.deptId()).append(" in('").append(StringUtils.join(deptIdList, "','")).append("')");
        }

        //没有本部门数据权限，也能查询本人数据
        if (dataFilter.user()) {
            if (deptIdList.size() > 0) {
                sqlFilter.append(" or ");
            }
            sqlFilter.append(tableAlias).append(dataFilter.userId()).append("=").append(user.getId());
        }

        sqlFilter.append(")");

        if (sqlFilter.toString().trim().equals("()")) {
            return null;
        }

        return sqlFilter.toString();
    }
}
