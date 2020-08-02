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
import com.cpdms.model.dinning.BaseEmp;
import com.cpdms.service.dinning.BaseEmpService;
import com.github.pagehelper.PageInfo;

@Controller
@RequestMapping("BaseEmpController")

public class BaseEmpController extends BaseController{
	
	private String prefix = "dinning/baseEmp";
	@Autowired
	private BaseEmpService baseEmpService;
	
	@GetMapping("view")
	@RequiresPermissions("dinning:baseEmp:view")
    public String view(ModelMap model)
    {	
		String str="员工信息，云闪付给数据";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }
	
	//@Log(title = "员工信息，云闪付给数据集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("dinning:baseEmp:list")
	@ResponseBody
	public Object list(Tablepar tablepar,String searchTxt){
		PageInfo<BaseEmp> page=baseEmpService.list(tablepar,searchTxt) ; 
		TableSplitResult<BaseEmp> result=new TableSplitResult<BaseEmp>(page.getPageNum(), page.getTotal(), page.getList()); 
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
	
	//@Log(title = "员工信息，云闪付给数据新增", action = "111")
	@PostMapping("add")
	@RequiresPermissions("dinning:baseEmp:add")
	@ResponseBody
	public AjaxResult add(BaseEmp baseEmp){
		int b=baseEmpService.insertSelective(baseEmp);
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
	//@Log(title = "员工信息，云闪付给数据删除", action = "111")
	@PostMapping("remove")
	@RequiresPermissions("dinning:baseEmp:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=baseEmpService.deleteByPrimaryKey(ids);
		if(b>0){
			return success();
		}else{
			return error();
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
        mmap.put("BaseEmp", baseEmpService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }
	
	/**
     * 修改保存
     */
    //@Log(title = "员工信息，云闪付给数据修改", action = "111")
    @RequiresPermissions("dinning:baseEmp:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(BaseEmp record)
    {
        return toAjax(baseEmpService.updateByPrimaryKeySelective(record));
    }

    
    

	
}
