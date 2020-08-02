package com.cpdms.controller.admin;

import org.apache.shiro.authz.annotation.RequiresPermissions;
import org.springframework.stereotype.Controller;
import org.springframework.ui.ModelMap;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;

import com.cpdms.common.base.BaseController;
import com.cpdms.model.custom.Service;
import com.cpdms.model.custom.TitleVo;

/**
 * 服务器信息Controller
 * @ClassName: ServiceController
 * @author fuce
 * @date 2019-06-23 00:55
 * @version V1.0
 */
@Controller

@RequestMapping("ServiceController")
public class ServiceController extends BaseController{

	//跳转页面参数
	private String prefix = "admin/service";
	
	@GetMapping("view")
	@RequiresPermissions("system:service:view")
    public String view(ModelMap model)
    {	
		
		String str="服务器";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
		
		model.addAttribute("service", new Service());
		
        return prefix + "/list";
    }
}
