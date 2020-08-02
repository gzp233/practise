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
import com.cpdms.model.dinning.BizOrdFood;
import com.cpdms.service.dinning.BizOrdFoodService;
import com.github.pagehelper.PageInfo;

@Controller
@RequestMapping("BizOrdFoodController")

public class BizOrdFoodController extends BaseController{
	
	private String prefix = "dinning/bizOrdFood";
	@Autowired
	private BizOrdFoodService bizOrdFoodService;
	
	@GetMapping("view")
	@RequiresPermissions("dinning:bizOrdFood:view")
    public String view(ModelMap model)
    {	
		String str="预定订单菜品";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }
	
	//@Log(title = "预定订单菜品集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("dinning:bizOrdFood:list")
	@ResponseBody
	public Object list(Tablepar tablepar,String searchTxt){
		PageInfo<BizOrdFood> page=bizOrdFoodService.list(tablepar,searchTxt) ; 
		TableSplitResult<BizOrdFood> result=new TableSplitResult<BizOrdFood>(page.getPageNum(), page.getTotal(), page.getList()); 
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
	
	//@Log(title = "预定订单菜品新增", action = "111")
	@PostMapping("add")
	@RequiresPermissions("dinning:bizOrdFood:add")
	@ResponseBody
	public AjaxResult add(BizOrdFood bizOrdFood){
		int b=bizOrdFoodService.insertSelective(bizOrdFood);
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
	//@Log(title = "预定订单菜品删除", action = "111")
	@PostMapping("remove")
	@RequiresPermissions("dinning:bizOrdFood:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=bizOrdFoodService.deleteByPrimaryKey(ids);
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
	public int checkNameUnique(BizOrdFood bizOrdFood){
		int b=bizOrdFoodService.checkNameUnique(bizOrdFood);
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
        mmap.put("BizOrdFood", bizOrdFoodService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }
	
	/**
     * 修改保存
     */
    //@Log(title = "预定订单菜品修改", action = "111")
    @RequiresPermissions("dinning:bizOrdFood:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(BizOrdFood record)
    {
        return toAjax(bizOrdFoodService.updateByPrimaryKeySelective(record));
    }

    
    

	
}
