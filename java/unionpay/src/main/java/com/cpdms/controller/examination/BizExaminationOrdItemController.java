package com.cpdms.controller.examination;

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
import com.cpdms.model.examination.BizExaminationOrdItem;
import com.cpdms.service.examination.BizExaminationOrdItemService;

@Controller
@RequestMapping("BizExaminationOrdItemController")

public class BizExaminationOrdItemController extends BaseController {

	private String prefix = "examination/bizExaminationOrdItem";
	@Autowired
	private BizExaminationOrdItemService bizExaminationOrdItemService;

	@GetMapping("view")
    public String view(ModelMap model)
    {
		String str="体检预约项目表";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }

	//@Log(title = "体检预约项目表集合查询", action = "111")
	@PostMapping("list")
	@ResponseBody
	public Object list(Tablepar tablepar, String searchTxt){
		PageInfo<BizExaminationOrdItem> page=bizExaminationOrdItemService.list(tablepar,searchTxt) ;
		TableSplitResult<BizExaminationOrdItem> result=new TableSplitResult<BizExaminationOrdItem>(page.getPageNum(), page.getTotal(), page.getList());
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

	//@Log(title = "体检预约项目表新增", action = "111")
	@PostMapping("add")
	@ResponseBody
	public AjaxResult add(BizExaminationOrdItem bizExaminationOrdItem){
		int b=bizExaminationOrdItemService.insertSelective(bizExaminationOrdItem);
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
	//@Log(title = "体检预约项目表删除", action = "111")
	@PostMapping("remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=bizExaminationOrdItemService.deleteByPrimaryKey(ids);
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
	public int checkNameUnique(BizExaminationOrdItem bizExaminationOrdItem){
		int b=bizExaminationOrdItemService.checkNameUnique(bizExaminationOrdItem);
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
        mmap.put("BizExaminationOrdItem", bizExaminationOrdItemService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }

	/**
     * 修改保存
     */
    //@Log(title = "体检预约项目表修改", action = "111")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(BizExaminationOrdItem record)
    {
        return toAjax(bizExaminationOrdItemService.updateByPrimaryKeySelective(record));
    }





}
