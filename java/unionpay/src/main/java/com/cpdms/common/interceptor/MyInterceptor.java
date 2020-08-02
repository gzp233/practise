package com.cpdms.common.interceptor;

import javax.annotation.Resource;
import javax.servlet.ServletRequest;
import javax.servlet.ServletResponse;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import ch.qos.logback.classic.Logger;
import cn.hutool.json.JSONUtil;
import cn.hutool.log.Log;
import cn.hutool.log.LogFactory;
import com.cpdms.common.conf.EncryptConf;
import com.cpdms.common.domain.AjaxResult;
import com.cpdms.common.token.ApiTokenValid;
import com.cpdms.common.token.JwtToken;
import com.cpdms.common.token.TokenConstant;
import com.cpdms.redis.RedisUtil;
import com.cpdms.util.JwtUtil;
import com.cpdms.util.StringUtils;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.RequestMethod;
import org.springframework.web.method.HandlerMethod;
import org.springframework.web.servlet.HandlerInterceptor;
import org.springframework.web.servlet.ModelAndView;

import java.io.IOException;
import java.io.PrintWriter;
import java.util.concurrent.TimeUnit;

/**
 * 自定义拦截器
 * @author fc
 *
 */
public class MyInterceptor  implements HandlerInterceptor {
	private static final Log logger = LogFactory.get(MyInterceptor.class);
	@Autowired
	RedisUtil redisUtil;
	@Override
	public void afterCompletion(HttpServletRequest arg0, HttpServletResponse arg1, Object handler, Exception arg3)throws Exception {
		// System.out.println(">>>MyInterceptor1>>>>>>>在整个请求结束之后被调用，也就是在DispatcherServlet 渲染了对应的视图之后执行（主要是用于进行资源清理工作）");
	}

	@Override
	public void postHandle(HttpServletRequest arg0, HttpServletResponse arg1, Object handler, ModelAndView arg3)throws Exception {
		// System.out.println(">>>MyInterceptor1>>>>>>>请求处理之后进行调用，但是在视图被渲染之前（Controller方法调用之后）");
	}

	@Override
	public boolean preHandle(HttpServletRequest request, HttpServletResponse response, Object handler) throws Exception {
		//String url2=request.getScheme()+"://"+ request.getServerName();
		//设置前端的全局 地址，如果前端网页错乱请修改这儿
		request.setAttribute("rootPath", request.getContextPath());
		//System.out.println("xxxxxxxxx==="+"http://localhost:8081/");
		//System.out.println(">>>MyInterceptor1>>>>>>>在请求处理之前进行调用（Controller方法调用之前）");
		//设置跨域--开始

		if (request.getMethod().equals(RequestMethod.OPTIONS.name())) {
			setHeader(request,response);
			return true;
		}
		//设置跨域--结束
		ApiTokenValid annotation ;
		if(handler instanceof HandlerMethod) {
			annotation = ((HandlerMethod) handler).getMethodAnnotation(ApiTokenValid.class);
		}else{
			return true;
		}

		if(annotation == null){
			return true;
		}
	if(this.refreshToken(request,response)){
		return true;
	}else{
		respMsg(response,JSONUtil.toJsonStr(AjaxResult.unauthorizedCall()));
		//throw new RuntimeException("401");
		return false;
	}

		// 只有返回true才会继续向下执行，返回false取消当前请求
		//return true;
	}
	/**
			* 为response设置header，实现跨域
     */
	private void setHeader(HttpServletRequest request,HttpServletResponse response){
		//跨域的header设置
		response.setHeader("Access-control-Allow-Origin", request.getHeader("Origin"));
		response.setHeader("Access-Control-Allow-Methods", request.getMethod());
		response.setHeader("Access-Control-Allow-Credentials", "true");
		response.setHeader("Access-Control-Allow-Headers", request.getHeader("Access-Control-Request-Headers"));
		//防止乱码，适用于传输JSON数据
		response.setHeader("Content-Type","application/json;charset=UTF-8");
	}

	private long minusDiff(long curIime,long anchorTime){
		long nd = 1000 * 24 * 60 * 60;
		long nh = 1000 * 60 * 60;
		long nm = 1000 * 60;
		// long ns = 1000;
		// 获得两个时间的毫秒时间差异
		long diff = curIime - anchorTime;
		/*// 计算差多少天
		long day = diff / nd;
		// 计算差多少小时
		long hour = diff % nd / nh;*/
		// 计算差多少分钟
		long min = diff % nd % nh / nm;
		// 计算差多少秒//输出结果
		return min;
	}
	/**
	 * 此处为AccessToken刷新，进行判断RefreshToken是否过期，未过期就返回新的AccessToken且继续正常访问
	 */
	private boolean refreshToken(HttpServletRequest request, HttpServletResponse response) throws IOException {
		// 拿到当前Header中Authorization的AccessToken(Shiro中getAuthzHeader方法已经实现)
		//从header中获取token
		String token = request.getHeader("token");;
		//如果header中不存在token，则从参数中获取token
		if(StringUtils.isBlank(token)){
			token = request.getParameter("token");
		}
		if(StringUtils.isBlank(token)){
			return false;
		}else{
			// 获取当前Token的帐号信息
			String account = JwtUtil.getClaim(token, TokenConstant.TOKEN_ACCOUNT);
			//logger.info(account);
			// 判断Redis中RefreshToken是否存在
			if (redisUtil.hasKey(TokenConstant.REDIS_PREFIX + account)) {
				// Redis中RefreshToken还存在，获取RefreshToken的时间戳
				String currentTimeMillisRedis = redisUtil.get(TokenConstant.REDIS_PREFIX + account).toString();
				long accessTokenTimeMillis = Long.parseLong(currentTimeMillisRedis);
				// 获取当前AccessToken中的时间戳，与RefreshToken的时间戳对比，如果当前时间戳一致，进行AccessToken刷新
				if (JwtUtil.getClaim(token, TokenConstant.CURRENT_TIME_MILLIS).equals(currentTimeMillisRedis)) {
						// 获取当前最新时间戳
						long currentTimeMillis = System.currentTimeMillis();
						String strCurrentTimeMillis = String.valueOf(currentTimeMillis);
						//如果redis中时间与当前时间差距离刷新差小于配置时间差则重新验证，生成新token并通过header下发新token
						if(minusDiff(currentTimeMillis,accessTokenTimeMillis)>=EncryptConf.getAccessTokenExpireTime()){
							//此处可考虑重新进行用户鉴权
							redisUtil.setEx(TokenConstant.REDIS_PREFIX + account,strCurrentTimeMillis,Integer.parseInt(EncryptConf.getRefreshTokenExpireTime()), TimeUnit.MINUTES);
							token = JwtUtil.sign(account, strCurrentTimeMillis);
						}


					response.setHeader("Authorization", token);
					response.setHeader("Access-Control-Expose-Headers", "Authorization");
					//respMsg(response, JSONUtil.toJsonStr(AjaxResult.success(token)));
					return true;
				}
			}
			return false;
		}


	}

	private void respMsg(HttpServletResponse response, String jsonStr) throws IOException {
		response.setContentType("application/json; charset=utf-8");
		PrintWriter	out = response.getWriter();
		out.append(jsonStr);
	}
}
