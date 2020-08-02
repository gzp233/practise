package com.cpdms.controller;

import org.springframework.stereotype.Controller;
import org.springframework.ui.ModelMap;
import org.springframework.web.bind.annotation.GetMapping;

import com.cpdms.common.base.BaseController;
import com.cpdms.model.custom.TitleVo;

/**
 * 如果有前台这儿写前台访问方法
* @ClassName: IndexController
* @author fuce
* @date 2019-10-21 00:15
*
 */
@Controller
public class IndexController extends BaseController{
	

	@GetMapping("/")
	public String index(ModelMap map) {
		String str="前台";
		setTitle(map, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
    	return "index";
	}

	@GetMapping("/index")
	public String index2(ModelMap map) {
		String str="前台";
		setTitle(map, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
		return "index";
	}
}
