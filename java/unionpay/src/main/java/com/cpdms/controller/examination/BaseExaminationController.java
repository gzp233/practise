package com.cpdms.controller.examination;

import java.util.Date;
import java.util.HashMap;
import java.util.List;

import com.cpdms.common.token.ApiTokenValid;
import com.cpdms.model.examination.*;
import com.cpdms.service.examination.BaseExaminationItemService;
import org.apache.shiro.authz.annotation.RequiresPermissions;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.ui.ModelMap;
import org.springframework.web.bind.annotation.*;

import com.cpdms.common.base.BaseController;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.domain.AjaxResult;
import com.cpdms.model.custom.TableSplitResult;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.custom.TitleVo;
import com.cpdms.service.examination.BaseExaminationService;
import com.cpdms.service.examination.BaseHospitalService;
import com.cpdms.util.DateUtils;

@Controller
@RequestMapping("BaseExaminationController")

public class BaseExaminationController extends BaseController {

	private String prefix = "examination/baseExamination";
	@Autowired
	private BaseExaminationService baseExaminationService;
	@Autowired
    private BaseHospitalService baseHospitalService;
	@Autowired
    private BaseExaminationItemService baseExaminationItemService;

	@GetMapping("view")
	@RequiresPermissions("examination:baseExamination:view")
    public String view(ModelMap model)
    {
		String str="基础体检项目表";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }

	//@Log(title = "基础体检项目表集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("examination:baseExamination:list")
	@ResponseBody
	public Object list(Tablepar tablepar, String searchTxt){
	    BaseExamination baseExamination = new BaseExamination();
	    if (searchTxt != null && !searchTxt.equals("")) {
	        baseExamination.setExaminationName("%" + searchTxt + "%");
        }
		PageInfo<BaseExamination> page=baseExaminationService.list(tablepar,baseExamination) ;
		TableSplitResult<BaseExamination> result=new TableSplitResult<BaseExamination>(page.getPageNum(), page.getTotal(), page.getList());
		return  result;
	}

	@PostMapping(value = "listJson",produces="application/json;charset=utf-8")
    @ResponseBody
	@ApiTokenValid
    public Object listJson(@RequestBody HashMap<String, Object> params) {
	    if (params.get("pageNum") == null || params.get("pageSize") == null || params.get("hospitalId") == null) {
	        return error(402, "字段缺失");
        }
        Integer pageNum = (Integer)params.get("pageNum");
		Integer pageSize = (Integer)params.get("pageSize");
		String hospitalId = params.get("hospitalId").toString();

		Tablepar tablepar = new Tablepar();
		tablepar.setPageNum(pageNum);
		tablepar.setPageSize(pageSize);
		BaseExamination baseExamination = new BaseExamination();
		baseExamination.setHospitalId(hospitalId);
		baseExamination.setEndTime(DateUtils.format(new Date(), DateUtils.DATE_PATTERN));
        PageInfo<BaseExamination> page = baseExaminationService.list(tablepar, baseExamination);
        TableSplitResult<BaseExamination> result = new TableSplitResult<BaseExamination>(page.getPageNum(), page.getTotal(), page.getList());
        return result;
    }

     @PostMapping(value = "getOne", produces = "application/json;charset=utf-8")
    @ResponseBody
	 @ApiTokenValid
    public Object getOne(@RequestBody HashMap<String, Object> params) {
        String id = params.get("id").toString();
        if (id == null || id.equals("")) {
            return error(402, "ID不能为空");
        }
        return retobject(200, baseExaminationService.selectByPrimaryKey(id));
    }

	/**
     * 新增
     */

    @GetMapping("/add")
    public String add(ModelMap modelMap)
    {
        List<BaseHospital> baseHospitalList = baseHospitalService.getAll();
        modelMap.put("baseHospitalList",baseHospitalList);
        List<BaseExaminationItem> baseExaminationItemList = baseExaminationItemService.getAll();
        modelMap.put("baseExaminationItemList",baseExaminationItemList);

        return prefix + "/add";
    }

	//@Log(title = "基础体检项目表新增", action = "111")
	@PostMapping("add")
	@RequiresPermissions("examination:baseExamination:add")
	@ResponseBody
	public AjaxResult add(BaseExamination baseExamination, Model model, @RequestParam(value="itemIds", required = false)List<String> itemIds){
        if (baseExamination.getStartTime() == null || DateUtils.dateTime(DateUtils.YYYY_MM_DD, baseExamination.getStartTime()).before(new Date())) {
            return error("开始时间必须大于当前时间");
        }
        if (baseExamination.getEndTime() == null ||
                DateUtils.dateTime(DateUtils.YYYY_MM_DD, baseExamination.getEndTime()).before(DateUtils.dateTime(DateUtils.YYYY_MM_DD, baseExamination.getStartTime()))) {
            return error("结束时间不能小于开始时间");
        }
        if (itemIds == null || itemIds.size() == 0) {
            return error("体检项目至少选择一个");
        }
		int b=baseExaminationService.insertSelective(baseExamination, itemIds);
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
	//@Log(title = "基础体检项目表删除", action = "111")
	@PostMapping("remove")
	@RequiresPermissions("examination:baseExamination:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=baseExaminationService.deleteByPrimaryKey(ids);
		if(b>0){
			return success();
		}else{
			return error();
		}
	}

	/**
	 * 检查用户
	 * @param
	 * @return
	 */
	@PostMapping("checkNameUnique")
	@ResponseBody
	public int checkNameUnique(BaseExamination baseExamination){
		int b=baseExaminationService.checkNameUnique(baseExamination);
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
        BaseExamination baseExamination = baseExaminationService.selectByPrimaryKey(id);
        mmap.put("BaseExamination", baseExamination);
        List<BaseHospital> baseHospitalList = baseHospitalService.getAll();
        mmap.put("baseHospitalList",baseHospitalList);
        List<BaseExaminationItem> baseExaminationItemList = baseExaminationItemService.getAll();
        mmap.put("baseExaminationItemList",baseExaminationItemList);
        if (baseExamination.getBaseExaminationItemList().size() > 0) {
            for (BaseExaminationItem checked:baseExamination.getBaseExaminationItemList()) {
                for (BaseExaminationItem baseExaminationItem:baseExaminationItemList) {
                    if (checked.getId().equals(baseExaminationItem.getId())) {
                        baseExaminationItem.setIscheck(true);
                    }
                }
            }
        }

        return prefix + "/edit";
    }

	/**
     * 修改保存
     */
    //@Log(title = "基础体检项目表修改", action = "111")
    @RequiresPermissions("examination:baseExamination:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(BaseExamination record, Model model, @RequestParam(value="itemIds", required = false)List<String> itemIds)
    {
        if (record.getStartTime() == null || DateUtils.dateTime(DateUtils.YYYY_MM_DD, record.getStartTime()).before(new Date())) {
            return error("开始时间必须大于当前时间");
        }
        if (record.getEndTime() == null ||
                DateUtils.dateTime(DateUtils.YYYY_MM_DD, record.getEndTime()).before(DateUtils.dateTime(DateUtils.YYYY_MM_DD, record.getStartTime()))) {
            return error("结束时间不能小于开始时间");
        }
        if (itemIds == null || itemIds.size() == 0) {
            return error("体检项目至少选择一个");
        }

        return toAjax(baseExaminationService.updateByPrimaryKeySelective(record, itemIds));
    }





}
