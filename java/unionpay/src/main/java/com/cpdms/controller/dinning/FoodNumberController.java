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
import com.cpdms.model.dinning.FoodNumber;
import com.cpdms.service.dinning.FoodNumberService;
import com.github.pagehelper.PageInfo;

@Controller
@RequestMapping("FoodNumberController")

public class FoodNumberController extends BaseController{

	private String prefix = "dinning/foodNumber";
	@Autowired
	private FoodNumberService foodNumberService;

	@GetMapping("view")
	@RequiresPermissions("dinning:foodNumber:view")
    public String view(ModelMap model)
    {
		String str="菜品期数";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }

	//@Log(title = "菜品期数集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("dinning:foodNumber:list")
	@ResponseBody
	public Object list(Tablepar tablepar,Integer searchTxt){
		PageInfo<FoodNumber> page=foodNumberService.list(tablepar,searchTxt) ;
		TableSplitResult<FoodNumber> result=new TableSplitResult<FoodNumber>(page.getPageNum(), page.getTotal(), page.getList());
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

	//@Log(title = "菜品期数新增", action = "111")
	@PostMapping("add")
	@RequiresPermissions("dinning:foodNumber:add")
	@ResponseBody
	public AjaxResult add(FoodNumber foodNumber){
        if (foodNumber.getBeginTime().after(foodNumber.getEndTime())) {
            return error("结束时间不能小于开始时间");
        }
		int b=foodNumberService.insertSelective(foodNumber);
		if(b>0){
			return success();
		}else{
			return error();
		}
	}

	// 获取所有菜品期数
    @PostMapping("getAll")
    @ResponseBody
    public Object getAll(){
		return retobject(200, foodNumberService.getAll());
	}

	/**
	 * 删除用户
	 * @param ids
	 * @return
	 */
	//@Log(title = "菜品期数删除", action = "111")
	@PostMapping("remove")
	@RequiresPermissions("dinning:foodNumber:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=foodNumberService.deleteByPrimaryKey(ids);
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
	public int checkNameUnique(FoodNumber foodNumber){
		int b=foodNumberService.checkNameUnique(foodNumber);
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
        mmap.put("FoodNumber", foodNumberService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }

	/**
     * 修改保存
     */
    //@Log(title = "菜品期数修改", action = "111")
    @RequiresPermissions("dinning:foodNumber:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(FoodNumber record)
    {
        if (record.getBeginTime().after(record.getEndTime())) {
            return error("结束时间不能小于开始时间");
        }
        return toAjax(foodNumberService.updateByPrimaryKeySelective(record));
    }





}
