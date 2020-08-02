package com.cpdms.controller.hair;

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
import com.cpdms.model.hair.BizHairOrdHair;
import com.cpdms.service.hair.BizHairOrdHairService;
import com.github.pagehelper.PageInfo;

@Controller
@RequestMapping("BizHairOrdHairController")

public class BizHairOrdHairController extends BaseController {

	private String prefix = "hair/bizHairOrdHair";
	@Autowired
	private BizHairOrdHairService bizHairOrdHairService;

	@GetMapping("view")
    public String view(ModelMap model)
    {
		String str="美发订单项目表";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }

	//@Log(title = "美发订单项目表集合查询", action = "111")
	@PostMapping("list")
	@ResponseBody
	public Object list(Tablepar tablepar, String searchTxt){
		PageInfo<BizHairOrdHair> page=bizHairOrdHairService.list(tablepar,searchTxt) ;
		TableSplitResult<BizHairOrdHair> result=new TableSplitResult<BizHairOrdHair>(page.getPageNum(), page.getTotal(), page.getList());
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

	//@Log(title = "美发订单项目表新增", action = "111")
	@PostMapping("add")
	@ResponseBody
	public AjaxResult add(BizHairOrdHair bizHairOrdHair){
		int b=bizHairOrdHairService.insertSelective(bizHairOrdHair);
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
	//@Log(title = "美发订单项目表删除", action = "111")
	@PostMapping("remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=bizHairOrdHairService.deleteByPrimaryKey(ids);
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
	public int checkNameUnique(BizHairOrdHair bizHairOrdHair){
		int b=bizHairOrdHairService.checkNameUnique(bizHairOrdHair);
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
        mmap.put("BizHairOrdHair", bizHairOrdHairService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }

	/**
     * 修改保存
     */
    //@Log(title = "美发订单项目表修改", action = "111")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(BizHairOrdHair record)
    {
        return toAjax(bizHairOrdHairService.updateByPrimaryKeySelective(record));
    }





}
