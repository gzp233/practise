package com.cpdms.controller.examination;

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
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.domain.AjaxResult;
import com.cpdms.model.custom.TableSplitResult;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.custom.TitleVo;
import com.cpdms.model.examination.BaseHospital;
import com.cpdms.service.examination.BaseHospitalService;

@Controller
@RequestMapping("BaseHospitalController")

public class BaseHospitalController extends BaseController {

	private String prefix = "examination/baseHospital";
	@Autowired
	private BaseHospitalService baseHospitalService;

	@GetMapping("view")
	@RequiresPermissions("examination:baseHospital:view")
    public String view(ModelMap model)
    {
		String str="医院列表";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }

	//@Log(title = "医院列表集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("examination:baseHospital:list")
	@ResponseBody
	public Object list(Tablepar tablepar, String searchTxt){
		PageInfo<BaseHospital> page=baseHospitalService.list(tablepar,searchTxt) ;
		TableSplitResult<BaseHospital> result=new TableSplitResult<BaseHospital>(page.getPageNum(), page.getTotal(), page.getList());
		return  result;
	}

	@PostMapping(value = "getAll",produces="application/json;charset=utf-8")
	@ResponseBody
	@ApiTokenValid
	public Object getAll(){
		return retobject(200, baseHospitalService.getAll());
	}

	/**
     * 新增
     */

    @GetMapping("/add")
    public String add(ModelMap modelMap)
    {
        return prefix + "/add";
    }

	//@Log(title = "医院列表新增", action = "111")
	@PostMapping("add")
	@RequiresPermissions("examination:baseHospital:add")
	@ResponseBody
	public AjaxResult add(BaseHospital baseHospital){
		int b=baseHospitalService.insertSelective(baseHospital);
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
	//@Log(title = "医院列表删除", action = "111")
	@PostMapping("remove")
	@RequiresPermissions("examination:baseHospital:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=baseHospitalService.deleteByPrimaryKey(ids);
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
	public int checkNameUnique(BaseHospital baseHospital){
		int b=baseHospitalService.checkNameUnique(baseHospital);
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
        mmap.put("BaseHospital", baseHospitalService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }

	/**
     * 修改保存
     */
    //@Log(title = "医院列表修改", action = "111")
    @RequiresPermissions("examination:baseHospital:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(BaseHospital record)
    {
        return toAjax(baseHospitalService.updateByPrimaryKeySelective(record));
    }





}
