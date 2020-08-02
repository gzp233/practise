package com.cpdms.controller.personnel;

import com.cpdms.common.base.BaseController;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.domain.AjaxResult;
import com.cpdms.model.custom.TableSplitResult;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.custom.TitleVo;
import com.cpdms.model.personnel.BasePersonnel;
import com.cpdms.service.personnel.AsyncService;
import com.cpdms.service.personnel.BasePersonnelService;
import com.cpdms.util.StringUtils;
import org.apache.shiro.authz.annotation.RequiresPermissions;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.ui.ModelMap;
import org.springframework.web.bind.annotation.*;


@Controller
@RequestMapping("BasePersonnelController")
public class BasePersonnelController extends BaseController {

	private String prefix = "personnel/basePersonnel";
	@Autowired
	private BasePersonnelService basePersonnelService;
	@Autowired
	private AsyncService asyncService;

	@GetMapping("view")
	@RequiresPermissions("personnel:basePersonnel:view")
    public String view(ModelMap model)
    {
		String str="行员信息";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }

	//@Log(title = "行员信息集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("personnel:basePersonnel:list")
	@ResponseBody
	public Object list(Tablepar tablepar, BasePersonnel basePersonnel){
		PageInfo<BasePersonnel> page=basePersonnelService.list(tablepar,basePersonnel) ;
		TableSplitResult<BasePersonnel> result=new TableSplitResult<>(page.getPageNum(), page.getTotal(), page.getList());
		return  result;
	}

	@GetMapping("upload")
	@RequiresPermissions("personnel:basePersonnel:upload")
	public String upload(ModelMap model)
	{
		String str="文件上传";
		setTitle(model, new TitleVo("文件上传", str+"管理", true,"欢迎进入"+str+"页面", true, false));
		return prefix + "/upload";
	}

	@PostMapping("execute")
	@RequiresPermissions("personnel:basePersonnel:execute")
	@ResponseBody
	public AjaxResult execute(Model model, @RequestParam(value="dataId", required = false) String fileName){
		if (fileName.equals("")) {
			return error("文件不能为空");
		}
		asyncService.dealZip(fileName);

		return success();
	}

	//  获取详情
	@PostMapping(value = "sync")
	@ResponseBody
	public AjaxResult sync(@RequestParam(value = "id", required = true) String id) {
		if (basePersonnelService.syncById(id)) {
			return success();
		}

		return error("同步失败");
	}

	/**
     * 新增
     */

    @GetMapping("/add")
    public String add(ModelMap modelMap)
    {
        return prefix + "/add";
    }

	//@Log(title = "行员信息新增", action = "111")
	@PostMapping("add")
	@RequiresPermissions("personnel:basePersonnel:add")
	@ResponseBody
	public AjaxResult add(BasePersonnel basePersonnel){
    	if (StringUtils.isEmpty(basePersonnel.getPersonnelNo())) {
    		return error("员工编号不能为空");
		}
		if (StringUtils.isEmpty(basePersonnel.getCardNo())) {
			return error("饭卡号不能为空");
		}
		if (StringUtils.isEmpty(basePersonnel.getDeptName())) {
			return error("部门不能为空");
		}
		if (StringUtils.isEmpty(basePersonnel.getPersonnelName())) {
			return error("员工姓名不能为空");
		}
		if (StringUtils.isEmpty(basePersonnel.getPersonnelMobile())) {
			return error("手机号不能为空");
		}
		if (StringUtils.isEmpty(basePersonnel.getPhotoName())) {
			return error("图片不能为空");
		}
//		if (basePersonnel.getEntryTime() == null) {
//			return error("入职时间不能为空");
//		}
		String msg=basePersonnelService.insertSelective2(basePersonnel);
		if(msg.equals("")){
			return success();
		}else{
			return error(msg);
		}
	}

	/**
	 * 删除用户
	 * @param ids
	 * @return
	 */
	//@Log(title = "行员信息删除", action = "111")
	@PostMapping("remove")
	@RequiresPermissions("personnel:basePersonnel:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=basePersonnelService.deleteByPrimaryKey(ids);
		if(b>0){
			return success();
		}else{
			return error();
		}
	}

	/**
	 * 检查用户
	 *
	 * @return
	 */
	@PostMapping("checkNameUnique")
	@ResponseBody
	public int checkNameUnique(BasePersonnel basePersonnel){
		int b=basePersonnelService.checkNameUnique(basePersonnel);
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
        mmap.put("BasePersonnel", basePersonnelService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }

	/**
     * 修改保存
     */
    //@Log(title = "行员信息修改", action = "111")
    @RequiresPermissions("personnel:basePersonnel:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(BasePersonnel record)
    {
		if (StringUtils.isEmpty(record.getPersonnelNo())) {
			return error("员工编号不能为空");
		}
		if (StringUtils.isEmpty(record.getCardNo())) {
			return error("饭卡号不能为空");
		}
		if (StringUtils.isEmpty(record.getDeptName())) {
			return error("部门不能为空");
		}
		if (StringUtils.isEmpty(record.getPersonnelName())) {
			return error("员工姓名不能为空");
		}
		if (StringUtils.isEmpty(record.getPersonnelMobile())) {
			return error("手机号不能为空");
		}
//		if (record.getEntryTime() == null) {
//			return error("入职时间不能为空");
//		}
		String msg=basePersonnelService.updateByPrimaryKeySelective2(record);
		if(msg.equals("")){
			return success();
		}else{
			return error(msg);
		}
    }





}
