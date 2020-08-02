package com.cpdms.controller.hair;

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
import com.cpdms.model.hair.BaseHairSrv;
import com.cpdms.service.hair.BaseHairSrvService;

@Controller
@RequestMapping("BaseHairSrvController")

public class BaseHairSrvController extends BaseController {

	private String prefix = "hair/baseHairSrv";
	@Autowired
	private BaseHairSrvService baseHairSrvService;

	@GetMapping("view")
	@RequiresPermissions("hair:baseHairSrv:view")
    public String view(ModelMap model)
    {
		String str="服务类型";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }

	//@Log(title = "服务类型表集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("hair:baseHairSrv:list")
	@ResponseBody
	public Object list(Tablepar tablepar, String searchTxt){
		PageInfo<BaseHairSrv> page=baseHairSrvService.list(tablepar,searchTxt) ;
		TableSplitResult<BaseHairSrv> result=new TableSplitResult<BaseHairSrv>(page.getPageNum(), page.getTotal(), page.getList());
		return  result;
	}

	@PostMapping(value = "getAll",produces="application/json;charset=utf-8")
	@ResponseBody
	@ApiTokenValid
	public Object getAll(){
		return baseHairSrvService.all();
	}

	/**
     * 新增
     */

    @GetMapping("/add")
    public String add(ModelMap modelMap)
    {
        return prefix + "/add";
    }

	//@Log(title = "服务类型表新增", action = "111")
	@PostMapping("add")
	@RequiresPermissions("hair:baseHairSrv:add")
	@ResponseBody
	public AjaxResult add(BaseHairSrv baseHairSrv){
		int b=baseHairSrvService.insertSelective(baseHairSrv);
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
	//@Log(title = "服务类型表删除", action = "111")
	@PostMapping("remove")
	@RequiresPermissions("hair:baseHairSrv:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=baseHairSrvService.deleteByPrimaryKey(ids);
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
	public int checkNameUnique(BaseHairSrv baseHairSrv){
		int b=baseHairSrvService.checkNameUnique(baseHairSrv);
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
        mmap.put("BaseHairSrv", baseHairSrvService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }

	/**
     * 修改保存
     */
    //@Log(title = "服务类型表修改", action = "111")
    @RequiresPermissions("hair:baseHairSrv:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(BaseHairSrv record)
    {
        return toAjax(baseHairSrvService.updateByPrimaryKeySelective(record));
    }





}
