package com.cpdms.controller.dinning;

import com.cpdms.common.token.ApiTokenValid;
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
import com.cpdms.model.dinning.BaseTimeRange;
import com.cpdms.service.dinning.BaseTimeRangeService;
import com.github.pagehelper.PageInfo;

@Controller
@RequestMapping("BaseTimeRangeController")

public class BaseTimeRangeController extends BaseController{

	private String prefix = "dinning/baseTimeRange";
	@Autowired
	private BaseTimeRangeService baseTimeRangeService;

	@GetMapping("view")
	@RequiresPermissions("dinning:baseTimeRange:view")
    public String view(ModelMap model)
    {
		String str="餐段设置";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }

	//@Log(title = "餐段设置集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("dinning:baseTimeRange:list")
	@ResponseBody
	public Object list(Tablepar tablepar,String searchTxt){
		PageInfo<BaseTimeRange> page=baseTimeRangeService.list(tablepar,searchTxt) ;
		TableSplitResult<BaseTimeRange> result=new TableSplitResult<BaseTimeRange>(page.getPageNum(), page.getTotal(), page.getList());
		return  result;
	}

	//@Log(title = "获取所有可用餐段设置", action = "111")
	@PostMapping("getAll")
//	@RequiresPermissions("dinning:baseTimeRange:getAll")
	@ResponseBody
	@ApiTokenValid
	public Object getAll(){
		return retobject(200, baseTimeRangeService.getAll());
	}

	/**
     * 新增
     */

    @GetMapping("/add")
    public String add(ModelMap modelMap)
    {
        return prefix + "/add";
    }

	//@Log(title = "餐段设置新增", action = "111")
	@PostMapping("add")
	@RequiresPermissions("dinning:baseTimeRange:add")
	@ResponseBody
	public AjaxResult add(BaseTimeRange baseTimeRange){
        String sTime = baseTimeRange.getStartTime();
        String eTime = baseTimeRange.getEndDate();
        if (sTime.trim().length() == 4) {
            baseTimeRange.setStartTime("0" + sTime);
        }
        if (eTime.trim().length() == 4) {
            baseTimeRange.setEndDate("0" + eTime);
        }
		int b=baseTimeRangeService.insertSelective(baseTimeRange);
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
	//@Log(title = "餐段设置删除", action = "111")
	@PostMapping("remove")
	@RequiresPermissions("dinning:baseTimeRange:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=baseTimeRangeService.deleteByPrimaryKey(ids);
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
	public int checkNameUnique(BaseTimeRange baseTimeRange){
		int b=baseTimeRangeService.checkNameUnique(baseTimeRange);
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
        mmap.put("BaseTimeRange", baseTimeRangeService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }

	/**
     * 修改保存
     */
    //@Log(title = "餐段设置修改", action = "111")
    @RequiresPermissions("dinning:baseTimeRange:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(BaseTimeRange record)
    {
        String sTime = record.getStartTime();
        String eTime = record.getEndDate();
        if (sTime.trim().length() == 4) {
            record.setStartTime("0" + sTime);
        }
        if (eTime.trim().length() == 4) {
            record.setEndDate("0" + eTime);
        }
        return toAjax(baseTimeRangeService.updateByPrimaryKeySelective(record));
    }





}
