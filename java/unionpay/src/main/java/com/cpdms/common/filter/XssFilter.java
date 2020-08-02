package com.cpdms.common.filter;

import cn.hutool.http.HtmlUtil;
import cn.hutool.log.LogFactory;

import javax.servlet.*;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.IOException;
import cn.hutool.log.Log;

public class XssFilter implements Filter {
    Log logger = LogFactory.get(XssFilter.class);
    @Override
    public void init(FilterConfig filterConfig) throws ServletException {

    }

    @Override
    public void doFilter(ServletRequest request, ServletResponse response, FilterChain chain)
            throws IOException, ServletException {
       /* HttpServletRequest req = (HttpServletRequest) request;
        HttpServletResponse res = (HttpServletResponse) response;
        String path = req.getRequestURI().substring(req.getContextPath().length()).replaceAll("[/]+$", "");
        if(path.contains("captcha")){
            chain.doFilter(request, response);
        }else{*/
            // logger.info("do filter start:");
            XssHttpServletRequestWrapper xssRequest = new XssHttpServletRequestWrapper(
                    (HttpServletRequest) request);
           // xssHttpResponseWrapper xssResponse = new xssHttpResponseWrapper((HttpServletResponse) response);
            chain.doFilter(xssRequest, response);
          /*  byte[] content = xssResponse.getContent();//获取返回值
            //判断是否有值
            if (content.length > 0) {

                String str = new String(content, "UTF-8");
                logger.info("返回值:" + str);


              String  ciphertext = str;
                try {
                    //......根据需要处理返回值
                    ciphertext =  HtmlUtil.filter(str);
                    logger.info("过滤后:" + ciphertext);
                } catch (Exception e) {
                    logger.error("filter response exception :" + e);
                }
                //把返回值输出到客户端
                ServletOutputStream out = response.getOutputStream();
                out.write(ciphertext.getBytes());
                out.flush();
                out.close();
                // logger.info("do filter End:");

            }
        }*/


    }


    @Override
    public void destroy() {

    }
}
