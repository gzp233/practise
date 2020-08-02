package com.cpdms.controller.admin;

import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.ModelMap;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.ResponseBody;

import com.cpdms.common.base.BaseController;
import com.cpdms.model.auto.SysArea;
import com.cpdms.model.auto.SysAreaExample;
import com.cpdms.model.auto.SysCity;
import com.cpdms.model.auto.SysCityExample;
import com.cpdms.model.auto.SysProvinceExample;
import com.cpdms.model.auto.SysStreet;
import com.cpdms.model.auto.SysStreetExample;
import com.cpdms.model.custom.TitleVo;
import com.cpdms.service.SysAreaService;
import com.cpdms.service.SysCityService;
import com.cpdms.service.SysProvinceService;
import com.cpdms.service.SysStreetService;

/**
 * 省份联动controller
* @ClassName: ProvinceLinkageController
* @author fuce
* @date 2019-10-05 11:19
*
 */
@Controller
@RequestMapping("ProvinceLinkageController")

public class ProvinceLinkageController  extends BaseController{
	@Autowired
	private SysProvinceService sysProvinceService;
	@Autowired
	private SysCityService sysCityService;
	@Autowired
	private SysAreaService sysAreaService;
	
	@Autowired
	private SysStreetService sysStreetService;
	
	private String prefix = "admin/province";
	
	
	@GetMapping("view")
    public String view(ModelMap model)
    {	
		String str="省份联动";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
		model.addAttribute("provinceList",sysProvinceService.selectByExample(new SysProvinceExample()));
        return prefix + "/list";
    }
	//市查询
	@GetMapping("getCity")
	@ResponseBody
	public List<SysCity> getCity(String pid){
		SysCityExample example=new SysCityExample();
		example.createCriteria().andProvinceCodeEqualTo(pid);
		return sysCityService.selectByExample(example);
		
	}
	
	//区查询
	@GetMapping("getArea")
	@ResponseBody
	public List<SysArea> getArea(String pid){
		SysAreaExample example=new SysAreaExample();
		example.createCriteria().andCityCodeEqualTo(pid);
		return sysAreaService.selectByExample(example);
		
	}
	
	//街道查询
	@GetMapping("getStreet")
	@ResponseBody
	public List<SysStreet> getStreet(String pid){
		SysStreetExample example=new SysStreetExample();
		example.createCriteria().andAreaCodeEqualTo(pid);
		return sysStreetService.selectByExample(example);
		
	}
		
	
}
