package com.cpdms.controller.admin;

import cn.hutool.log.Log;
import cn.hutool.log.LogFactory;
import com.cpdms.common.base.BaseController;
import com.cpdms.common.conf.EncryptConf;
import com.cpdms.common.domain.AjaxResult;
import com.cpdms.common.token.TokenConstant;
import com.cpdms.redis.RedisUtil;
import com.cpdms.util.JwtUtil;
import org.apache.shiro.SecurityUtils;
import org.apache.shiro.authc.*;
import org.apache.shiro.subject.Subject;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.ResponseBody;

import javax.servlet.http.HttpServletResponse;
import java.util.concurrent.TimeUnit;

@Controller
public class AuthTokenController extends BaseController {
    private static final Log logger = LogFactory.get(AuthTokenController.class);
    @Autowired
    RedisUtil redisUtil;
    @PostMapping(value = "oauth")
    @ResponseBody
public AjaxResult oauth(String account, String password, HttpServletResponse response){
        try {

            Subject currentUser = SecurityUtils.getSubject();
            UsernamePasswordToken useraccount = new UsernamePasswordToken(account, password);
            System.out.println(password);
            currentUser.login(useraccount);
            String currentTimeMillis = String.valueOf(System.currentTimeMillis());
            redisUtil.setEx(TokenConstant.REDIS_PREFIX + account,currentTimeMillis,Integer.parseInt(EncryptConf.getRefreshTokenExpireTime()), TimeUnit.MINUTES);
            String token = JwtUtil.sign(account, currentTimeMillis);
            response.setHeader("Authorization", token);
            response.setHeader("Access-Control-Expose-Headers", "Authorization");
            return AjaxResult.success(token);
        }catch (UnknownAccountException uae) {
            logger.info("对用户[" + account + "]进行登录验证..验证未通过,未知账户");
            return AjaxResult.error("用户或密码错误");

        } catch (IncorrectCredentialsException ice) {
            logger.info("对用户[" + account + "]进行登录验证..验证未通过,错误的凭证");
            return AjaxResult.error("用户或密码错误");
        } catch (LockedAccountException lae) {
            logger.info("对用户[" + account + "]进行登录验证..验证未通过,账户已锁定");
            return AjaxResult.error("用户或密码错误");
        } catch (ExcessiveAttemptsException eae) {
            logger.info("对用户[" + account + "]进行登录验证..验证未通过,错误次数过多");
            return AjaxResult.error("用户或密码错误");
        } catch (Exception ae) {
            //通过处理Shiro的运行时AuthenticationException就可以控制用户登录失败或密码错误时的情景
            logger.info("对用户[" + account + "]进行登录验证..验证未通过,堆栈轨迹如下");
            ae.printStackTrace();
            return AjaxResult.error("用户或密码错误");
        }


}


}
