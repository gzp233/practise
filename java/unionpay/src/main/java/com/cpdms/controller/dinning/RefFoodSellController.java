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
import com.cpdms.model.dinning.RefFoodSell;
import com.cpdms.service.dinning.RefFoodSellService;
import com.github.pagehelper.PageInfo;

@Controller
@RequestMapping("RefFoodSellController")

public class RefFoodSellController extends BaseController{
	
	private String prefix = "dinning/refFoodSell";
	@Autowired
	private RefFoodSellService refFoodSellService;
	
	@GetMapping("view")
	@RequiresPermissions("dinning:refFoodSell:view")
    public String view(ModelMap model)
    {	
		String str="菜品供应方式关系";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }
	
	//@Log(title = "菜品供应方式关系集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("dinning:refFoodSell:list")
	@ResponseBody
	public Object list(Tablepar tablepar,String searchTxt){
		PageInfo<RefFoodSell> page=refFoodSellService.list(tablepar,searchTxt) ; 
		TableSplitResult<RefFoodSell> result=new TableSplitResult<RefFoodSell>(page.getPageNum(), page.getTotal(), page.getList()); 
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
	
	//@Log(title = "菜品供应方式关系新增", action = "111")
	@PostMapping("add")
	@RequiresPermissions("dinning:refFoodSell:add")
	@ResponseBody
	public AjaxResult add(RefFoodSell refFoodSell){
		int b=refFoodSellService.insertSelective(refFoodSell);
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
	//@Log(title = "菜品供应方式关系删除", action = "111")
	@PostMapping("remove")
	@RequiresPermissions("dinning:refFoodSell:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=refFoodSellService.deleteByPrimaryKey(ids);
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
	public int checkNameUnique(RefFoodSell refFoodSell){
		int b=refFoodSellService.checkNameUnique(refFoodSell);
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
        mmap.put("RefFoodSell", refFoodSellService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }
	
	/**
     * 修改保存
     */
    //@Log(title = "菜品供应方式关系修改", action = "111")
    @RequiresPermissions("dinning:refFoodSell:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(RefFoodSell record)
    {
        return toAjax(refFoodSellService.updateByPrimaryKeySelective(record));
    }

    
    

	
}
