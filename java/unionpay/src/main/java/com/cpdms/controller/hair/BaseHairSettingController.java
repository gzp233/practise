package com.cpdms.controller.hair;

import com.cpdms.common.base.BaseController;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.domain.AjaxResult;
import com.cpdms.model.custom.TableSplitResult;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.custom.TitleVo;
import com.cpdms.model.hair.BaseHairSetting;
import com.cpdms.service.hair.BaseHairSettingService;
import org.apache.shiro.authz.annotation.RequiresPermissions;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.ModelMap;
import org.springframework.web.bind.annotation.*;

import java.util.HashMap;


@Controller
@RequestMapping("BaseHairSettingController")
public class BaseHairSettingController extends BaseController {

	private String prefix = "hair/baseHairSetting";
	@Autowired
	private BaseHairSettingService baseHairSettingService;

	@GetMapping("view")
	@RequiresPermissions("hair:baseHairSetting:view")
    public String view(ModelMap model)
    {
		String str="";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }

	//@Log(title = "集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("hair:baseHairSetting:list")
	@ResponseBody
	public Object list(Tablepar tablepar){
		PageInfo<BaseHairSetting> page=baseHairSettingService.list(tablepar) ;
		TableSplitResult<BaseHairSetting> result=new TableSplitResult<BaseHairSetting>(page.getPageNum(), page.getTotal(), page.getList());
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

	//@Log(title = "新增", action = "111")
	@PostMapping("add")
	@RequiresPermissions("hair:baseHairSetting:add")
	@ResponseBody
	public AjaxResult add(BaseHairSetting baseHairSetting){
		int b=baseHairSettingService.insertSelective(baseHairSetting);
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
	//@Log(title = "删除", action = "111")
	@PostMapping("remove")
	@RequiresPermissions("hair:baseHairSetting:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=baseHairSettingService.deleteByPrimaryKey(ids);
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
	public int checkNameUnique(BaseHairSetting baseHairSetting){
		int b=baseHairSettingService.checkNameUnique(baseHairSetting);
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
        mmap.put("BaseHairSetting", baseHairSettingService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }

	/**
     * 修改保存
     */
    //@Log(title = "修改", action = "111")
    @RequiresPermissions("hair:baseHairSetting:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(BaseHairSetting record)
    {
        if (record.getSettingKey().equals("STEP_TIME")) {
            if (!record.getSettingValue().equals("30") && !record.getSettingValue().equals("60") && !record.getSettingValue().equals("90")) {
                return error("时段时长必须为30,60或90");
            }
        }
        // 设置生效时间
        return toAjax(baseHairSettingService.updateByPrimaryKeySelective(record));
    }


}
