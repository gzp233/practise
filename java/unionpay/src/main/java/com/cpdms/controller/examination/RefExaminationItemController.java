package com.cpdms.controller.examination;

import com.cpdms.common.base.BaseController;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.domain.AjaxResult;
import com.cpdms.model.custom.TableSplitResult;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.custom.TitleVo;
import com.cpdms.model.examination.RefExaminationItem;
import com.cpdms.service.examination.RefExaminationItemService;
import org.apache.shiro.authz.annotation.RequiresPermissions;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.ModelMap;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.ResponseBody;


@Controller
@RequestMapping("RefExaminationItemController")
public class RefExaminationItemController extends BaseController {

	private String prefix = "gen/refExaminationItem";
	@Autowired
	private RefExaminationItemService refExaminationItemService;

	@GetMapping("view")
	@RequiresPermissions("gen:refExaminationItem:view")
    public String view(ModelMap model)
    {
		String str="";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }

	//@Log(title = "集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("gen:refExaminationItem:list")
	@ResponseBody
	public Object list(Tablepar tablepar, String searchTxt){
		PageInfo<RefExaminationItem> page=refExaminationItemService.list(tablepar,searchTxt) ;
		TableSplitResult<RefExaminationItem> result=new TableSplitResult<RefExaminationItem>(page.getPageNum(), page.getTotal(), page.getList());
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
	@RequiresPermissions("gen:refExaminationItem:add")
	@ResponseBody
	public AjaxResult add(RefExaminationItem refExaminationItem){
		int b=refExaminationItemService.insertSelective(refExaminationItem);
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
	@RequiresPermissions("gen:refExaminationItem:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=refExaminationItemService.deleteByPrimaryKey(ids);
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
	public int checkNameUnique(RefExaminationItem refExaminationItem){
		int b=refExaminationItemService.checkNameUnique(refExaminationItem);
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
        mmap.put("RefExaminationItem", refExaminationItemService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }

	/**
     * 修改保存
     */
    //@Log(title = "修改", action = "111")
    @RequiresPermissions("gen:refExaminationItem:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(RefExaminationItem record)
    {
        return toAjax(refExaminationItemService.updateByPrimaryKeySelective(record));
    }





}
