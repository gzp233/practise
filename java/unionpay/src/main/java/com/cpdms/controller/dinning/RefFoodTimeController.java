package com.cpdms.controller.dinning;

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
import com.cpdms.model.custom.TableSplitResult;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.custom.TitleVo;
import com.cpdms.model.dinning.RefFoodTime;
import com.cpdms.service.dinning.RefFoodTimeService;
import com.github.pagehelper.PageInfo;

@Controller
@RequestMapping("RefFoodTimeController")

public class RefFoodTimeController extends BaseController{
	
	private String prefix = "dinning/refFoodTime";
	@Autowired
	private RefFoodTimeService refFoodTimeService;
	
	@GetMapping("view")
	@RequiresPermissions("dinning:refFoodTime:view")
    public String view(ModelMap model)
    {	
		String str="菜品餐段关系";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }
	
	//@Log(title = "菜品餐段关系集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("dinning:refFoodTime:list")
	@ResponseBody
	public Object list(Tablepar tablepar,String searchTxt){
		PageInfo<RefFoodTime> page=refFoodTimeService.list(tablepar,searchTxt) ; 
		TableSplitResult<RefFoodTime> result=new TableSplitResult<RefFoodTime>(page.getPageNum(), page.getTotal(), page.getList()); 
		return  result;
	}
	
	/**
     * 新增
     */

    @GetMapping("/add")
    public String add(ModelMap modelMap)
    {
        return prefix + "/add";
    }
	
	//@Log(title = "菜品餐段关系新增", action = "111")
	@PostMapping("add")
	@RequiresPermissions("dinning:refFoodTime:add")
	@ResponseBody
	public AjaxResult add(RefFoodTime refFoodTime){
		int b=refFoodTimeService.insertSelective(refFoodTime);
		if(b>0){
			return success();
		}else{
			return error();
		}
	}
	
	/**
	 * 删除用户
	 * @param ids
	 * @return
	 */
	//@Log(title = "菜品餐段关系删除", action = "111")
	@PostMapping("remove")
	@RequiresPermissions("dinning:refFoodTime:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=refFoodTimeService.deleteByPrimaryKey(ids);
		if(b>0){
			return success();
		}else{
			return error();
		}
	}
	
	/**
	 * 检查用户
	 * @param tsysUser
	 * @return
	 */
	@PostMapping("checkNameUnique")
	@ResponseBody
	public int checkNameUnique(RefFoodTime refFoodTime){
		int b=refFoodTimeService.checkNameUnique(refFoodTime);
		if(b>0){
			return 1;
		}else{
			return 0;
		}
	}
	
	
	/**
	 * 修改跳转
	 * @param id
	 * @param mmap
	 * @return
	 */
	@GetMapping("/edit/{id}")
    public String edit(@PathVariable("id") String id, ModelMap mmap)
    {
        mmap.put("RefFoodTime", refFoodTimeService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }
	
	/**
     * 修改保存
     */
    //@Log(title = "菜品餐段关系修改", action = "111")
    @RequiresPermissions("dinning:refFoodTime:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(RefFoodTime record)
    {
        return toAjax(refFoodTimeService.updateByPrimaryKeySelective(record));
    }

    
    

	
}
