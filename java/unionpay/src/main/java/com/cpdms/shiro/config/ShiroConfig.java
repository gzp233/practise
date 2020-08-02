package com.cpdms.shiro.config;

import at.pollux.thymeleaf.shiro.dialect.ShiroDialect;
import com.cpdms.common.conf.RedisConf;
import com.cpdms.common.exception.GlobalExceptionResolver;
import com.cpdms.shiro.service.MyShiroRealm;
import org.apache.shiro.authc.credential.HashedCredentialsMatcher;
import org.apache.shiro.cache.CacheManager;
import org.apache.shiro.cache.MemoryConstrainedCacheManager;
import org.apache.shiro.mgt.RememberMeManager;
import org.apache.shiro.realm.AuthorizingRealm;
import org.apache.shiro.realm.Realm;
import org.apache.shiro.spring.security.interceptor.AuthorizationAttributeSourceAdvisor;
import org.apache.shiro.spring.web.ShiroFilterFactoryBean;
import org.apache.shiro.web.mgt.CookieRememberMeManager;
import org.apache.shiro.web.mgt.DefaultWebSecurityManager;
import org.apache.shiro.web.servlet.Cookie;
import org.apache.shiro.web.servlet.SimpleCookie;
import org.apache.shiro.web.session.mgt.DefaultWebSessionManager;
import org.crazycake.shiro.RedisCacheManager;
import org.crazycake.shiro.RedisManager;
import org.crazycake.shiro.RedisSessionDAO;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.web.servlet.HandlerExceptionResolver;

import javax.annotation.Resource;


/**
 * 权限配置文件
 * @ClassName: ShiroConfiguration
 * @author fuce
 * @date 2018年8月25日
 *
 */
@Configuration
public class ShiroConfig {

@Resource
RedisConf redisConf;
	@Bean("shiroFilter")
	public ShiroFilterFactoryBean shiroFilterFactoryBean(org.apache.shiro.mgt.SecurityManager securityManager) {
		ShiroFilterFactoryBean shiroFilterFactoryBean = new ShiroFilterFactoryBean();
		//登录
		shiroFilterFactoryBean.setLoginUrl("/admin/login");
		//首页
		shiroFilterFactoryBean.setSuccessUrl("/");
		//错误页面，认证不通过跳转
		shiroFilterFactoryBean.setUnauthorizedUrl("/error/403");
		//页面权限控制
		shiroFilterFactoryBean.setFilterChainDefinitionMap(ShiroFilterMapFactory.shiroFilterMap());

		shiroFilterFactoryBean.setSecurityManager(securityManager);
		return shiroFilterFactoryBean;
	}

	@Bean
	public DefaultWebSecurityManager securityManager(Realm shiroRealm, CacheManager cacheManager, RememberMeManager manager) {
		DefaultWebSecurityManager securityManager = new DefaultWebSecurityManager();

		// 设置realm.
		securityManager.setRealm(shiroRealm);
		//记住Cookie
		securityManager.setRememberMeManager(rememberMeManager());
		securityManager.setCacheManager(shrioCacheManager());
		if(RedisConf.getIsRedisSession()){

			// 自定义session管理 使用redis
			securityManager.setSessionManager(sessionManager());

		}

		return securityManager;
	}
	@Bean
	public CacheManager shrioCacheManager(){
		//System.out.println("RedisConf redissession "  + RedisConf.getIsRedisSession());
		//System.out.println("RedisConf RedisConf.getHost() "  + RedisConf.getHost());
		if(RedisConf.getIsRedisSession()){
			return redisCacheManager();
		}else{
			return memCacheManager();
		}

	}

	private RedisCacheManager redisCacheManager() {

		RedisCacheManager redisCacheManager = new RedisCacheManager();
		redisCacheManager.setRedisManager(redisManager());
		redisCacheManager.setKeyPrefix("com:cpdms:");
		// 配置缓存的话要求放在session里面的实体类必须有个id标识 注：这里id为用户表中的主键，否-> 报：User must has getter for field: xx

		return redisCacheManager;
	}

	private CacheManager memCacheManager() {
		return new MemoryConstrainedCacheManager();
	}

	public RedisManager redisManager() {
		RedisManager redisManager = new RedisManager();
		redisManager.setHost(RedisConf.getHost());
		redisManager.setPort(RedisConf.getPort());
		redisManager.setExpire(RedisConf.getExpiretime());// 配置缓存过期时间
		redisManager.setTimeout(0);
		// redisManager.setPassword(password);
		return redisManager;
	}

	@Bean
	public DefaultWebSessionManager sessionManager() {
		DefaultWebSessionManager sessionManager = new DefaultWebSessionManager();
		sessionManager.setSessionDAO(redisSessionDAO());
		return sessionManager;
	}

	@Bean
	public RedisSessionDAO redisSessionDAO() {
		RedisSessionDAO redisSessionDAO = new RedisSessionDAO();
		redisSessionDAO.setRedisManager(redisManager());
		redisSessionDAO.setKeyPrefix("session:cpdms:");
		return redisSessionDAO;
	}




	@Bean
	public AuthorizingRealm shiroRealm(HashedCredentialsMatcher hashedCredentialsMatcher) {
		MyShiroRealm shiroRealm = new MyShiroRealm();

		//校验密码用到的算法
		shiroRealm.setCredentialsMatcher(hashedCredentialsMatcher);
		return shiroRealm;
	}
	@Bean
	public RememberMeManager rememberMeManager() {
		Cookie cookie = new SimpleCookie("rememberMe");
		cookie.setHttpOnly(true);//通过js脚本将无法读取到cookie信息
		cookie.setMaxAge(60 * 60 * 24);//cookie保存一天
		CookieRememberMeManager manager=new CookieRememberMeManager();
		manager.setCookie(cookie);
		return manager;
	}
	/**
	 * 加密算法
	 * @return
	 */
	@Bean
	public HashedCredentialsMatcher hashedCredentialsMatcher() {
		HashedCredentialsMatcher hashedCredentialsMatcher = new HashedCredentialsMatcher();
		hashedCredentialsMatcher.setHashAlgorithmName("MD5");//采用MD5 进行加密
		hashedCredentialsMatcher.setHashIterations(1);//加密次数
		return hashedCredentialsMatcher;
	}
	/**
	 * 启用shiro方言，这样能在页面上使用shiro标签
	 * @return
	 */
	@Bean
	public ShiroDialect shiroDialect() {
		return new ShiroDialect();
	}

	/**
	 * 启用shiro注解
	 *加入注解的使用，不加入这个注解不生效
	 */
	@Bean
	public AuthorizationAttributeSourceAdvisor getAuthorizationAttributeSourceAdvisor(org.apache.shiro.mgt.SecurityManager securityManager) {
		AuthorizationAttributeSourceAdvisor advisor = new AuthorizationAttributeSourceAdvisor();
		advisor.setSecurityManager(securityManager);
		return advisor;
	}

	/**
	 * 注册全局异常处理
	 * @return
	 */
	@Bean(name = "exceptionHandler")
	public HandlerExceptionResolver handlerExceptionResolver(){
		return new GlobalExceptionResolver();
	}

}
