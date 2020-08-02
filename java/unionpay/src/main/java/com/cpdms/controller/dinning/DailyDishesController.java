package com.cpdms.controller.dinning;

import java.util.HashMap;
import java.util.List;

import javax.validation.Valid;

import com.cpdms.common.token.ApiTokenValid;
import org.apache.shiro.authz.annotation.RequiresPermissions;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.ModelMap;
import org.springframework.validation.BindingResult;
import org.springframework.validation.ObjectError;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.ResponseBody;

import com.cpdms.common.base.BaseController;
import com.cpdms.common.domain.AjaxResult;
import com.cpdms.model.custom.TableSplitResult;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.custom.TitleVo;
import com.cpdms.model.dinning.DailyDishes;
import com.cpdms.model.dinning.FoodNumber;
import com.cpdms.service.dinning.DailyDishesService;
import com.cpdms.service.dinning.FoodNumberService;
import com.github.pagehelper.PageInfo;
import org.springframework.web.util.HtmlUtils;

@Controller
@RequestMapping("DailyDishesController")

public class DailyDishesController extends BaseController{

	private String prefix = "dinning/dailyDishes";
	@Autowired
	private DailyDishesService dailyDishesService;
	@Autowired
    private FoodNumberService foodNumberService;

	@GetMapping("view")
	@RequiresPermissions("dinning:dailyDishes:view")
    public String view(ModelMap model)
    {
		String str="每日菜品";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }

	//@Log(title = "每日菜品集合查询", action = "111")
	@PostMapping(value = "list", produces="application/json;charset=utf-8")
//	@RequiresPermissions("dinning:dailyDishes:list")
	@ResponseBody
	public Object list(Tablepar tablepar,String searchTxt){
		PageInfo<DailyDishes> page=dailyDishesService.list(tablepar,searchTxt) ;
		TableSplitResult<DailyDishes> result=new TableSplitResult<DailyDishes>(page.getPageNum(), page.getTotal(), page.getList());
		return  result;
	}

	@PostMapping(value = "listJson", produces="application/json;charset=utf-8")
	@ResponseBody
	@ApiTokenValid
    public Object listJson(@RequestBody HashMap<String, Object> params){
	     // 先获取期数
        FoodNumber foodNumbers = foodNumberService.getCurrent();
        if (foodNumbers == null) {
            return error(402, "当前无期数");
        }
	    Integer pageNum = (Integer)params.get("pageNum");
		Integer pageSize = (Integer)params.get("pageSize");
		Tablepar tablepar = new Tablepar();
		tablepar.setPageNum(pageNum);
		tablepar.setPageSize(pageSize);
		PageInfo<DailyDishes> page=dailyDishesService.dailyList(tablepar, foodNumbers.getId()) ;
		TableSplitResult<DailyDishes> result=new TableSplitResult<DailyDishes>(page.getPageNum(), page.getTotal(), page.getList());
		return  result;
	}

	@PostMapping(value = "dailyGet", produces="application/json;charset=utf-8")
	@ResponseBody
	@ApiTokenValid
    public Object dailyGet(@RequestBody HashMap<String, Object> params){
	    String id = "";

	    if (params.get("id") != null) {
	        id = HtmlUtils.htmlEscape(params.get("id").toString());;
        }
        if (id.equals("")) {
            return error(402, "ID不能为空");
        }
		return  dailyDishesService.dailyGet(id);
	}

	/**
     * 新增
     */

    @GetMapping("/add")
    public String add(ModelMap modelMap)
    {
        return prefix + "/add";
    }

	//@Log(title = "每日菜品新增", action = "111")
	@PostMapping("add")
	@RequiresPermissions("dinning:dailyDishes:add")
	@ResponseBody
	public AjaxResult add(DailyDishes dailyDishes){
		int b=dailyDishesService.insertSelective(dailyDishes);
		if(b>0){
			return success();
		}else{
			return error();
		}
	}

	/*批量添加
	*
	* */
	@PostMapping(value = "multiAdd", produces="application/json;charset=utf-8")
    @ResponseBody
    public AjaxResult multiAdd(@RequestBody @Valid List<DailyDishes> dailyDishesList, BindingResult result) {
	    if (result.hasErrors()) {
            List<ObjectError> list = result.getAllErrors();
			for (ObjectError error : list) {
				return retobject(402, error.getDefaultMessage());
			}
        }
		String m=dailyDishesService.saveDishes(dailyDishesList);
		if(m.equals("")){
			return success();
		}else{
			return error(402, m);
		}
    }

	/**
	 * 删除用户
	 * @param ids
	 * @return
	 */
	//@Log(title = "每日菜品删除", action = "111")
	@PostMapping("remove")
	@RequiresPermissions("dinning:dailyDishes:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=dailyDishesService.deleteByPrimaryKey(ids);
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
	public int checkNameUnique(DailyDishes dailyDishes){
		int b=dailyDishesService.checkNameUnique(dailyDishes);
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
        mmap.put("DailyDishes", dailyDishesService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }

	/**
     * 修改保存
     */
    //@Log(title = "每日菜品修改", action = "111")
    @RequiresPermissions("dinning:dailyDishes:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(DailyDishes record)
    {
        return toAjax(dailyDishesService.updateByPrimaryKeySelective(record));
    }





}
