package com.cpdms.controller.admin;

import org.apache.shiro.authz.annotation.RequiresPermissions;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.ModelMap;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.ResponseBody;

import com.cpdms.common.base.BaseController;
import com.cpdms.common.domain.AjaxResult;
import com.cpdms.model.auto.SysQuartzJobLog;
import com.cpdms.model.custom.TableSplitResult;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.custom.TitleVo;
import com.cpdms.service.SysQuartzJobLogService;
import com.github.pagehelper.PageInfo;

@Controller
@RequestMapping("SysQuartzJobLogController")

public class QuartzJobLogController extends BaseController{
	
	private String prefix = "admin/sysQuartzJobLog";
	@Autowired
	private SysQuartzJobLogService sysQuartzJobLogService;
	
	@GetMapping("view")
	@RequiresPermissions("gen:sysQuartzJobLog:view")
    public String view(ModelMap model)
    {	
		String str="定时任务调度日志表";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }
	
	//@Log(title = "定时任务调度日志表集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("gen:sysQuartzJobLog:list")
	@ResponseBody
	public Object list(Tablepar tablepar,String searchTxt){
		PageInfo<SysQuartzJobLog> page=sysQuartzJobLogService.list(tablepar,searchTxt) ; 
		TableSplitResult<SysQuartzJobLog> result=new TableSplitResult<SysQuartzJobLog>(page.getPageNum(), page.getTotal(), page.getList()); 
		return  result;
	}
	
	/**
	 * 查看详情
	 * @param modelMap
	 * @return
	 * @author fuce
	 * @Date 2019年9月14日 下午11:50:42
	 */
	 @GetMapping("/detail/{id}")
     public String detail(@PathVariable("id") String id,ModelMap modelMap)
     {
		SysQuartzJobLog log= sysQuartzJobLogService.selectByPrimaryKey(id);
		modelMap.put("SysQuartzJobLog", log);
        return prefix + "/detail";
     }
	 

	
	/**
	 * 删除用户
	 * @param ids
	 * @return
	 */
	//@Log(title = "定时任务调度日志表删除", action = "111")
	@PostMapping("remove")
	@RequiresPermissions("gen:sysQuartzJobLog:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=sysQuartzJobLogService.deleteByPrimaryKey(ids);
		if(b>0){
			return success();
		}else{
			return error();
		}
	}
    
    

	
}
