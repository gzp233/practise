package com.cpdms.controller.vote;

import com.cpdms.common.base.BaseController;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.domain.AjaxResult;
import com.cpdms.model.custom.TableSplitResult;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.custom.TitleVo;
import com.cpdms.model.vote.BaseVoteItem;
import com.cpdms.service.vote.BaseVoteItemService;
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
@RequestMapping("BaseVoteItemController")
public class BaseVoteItemController extends BaseController {

	private String prefix = "vote/baseVoteItem";
	@Autowired
	private BaseVoteItemService baseVoteItemService;

	@GetMapping("view")
	@RequiresPermissions("vote:baseVoteItem:view")
    public String view(ModelMap model)
    {
		String str="";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }

	//@Log(title = "集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("vote:baseVoteItem:list")
	@ResponseBody
	public Object list(Tablepar tablepar, String searchTxt){
		PageInfo<BaseVoteItem> page=baseVoteItemService.list(tablepar,searchTxt) ;
		TableSplitResult<BaseVoteItem> result=new TableSplitResult<BaseVoteItem>(page.getPageNum(), page.getTotal(), page.getList());
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
	@RequiresPermissions("vote:baseVoteItem:add")
	@ResponseBody
	public AjaxResult add(BaseVoteItem baseVoteItem){
		int b=baseVoteItemService.insertSelective(baseVoteItem);
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
	@RequiresPermissions("vote:baseVoteItem:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=baseVoteItemService.deleteByPrimaryKey(ids);
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
	public int checkNameUnique(BaseVoteItem baseVoteItem){
		int b=baseVoteItemService.checkNameUnique(baseVoteItem);
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
        mmap.put("BaseVoteItem", baseVoteItemService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }

	/**
     * 修改保存
     */
    //@Log(title = "修改", action = "111")
    @RequiresPermissions("vote:baseVoteItem:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(BaseVoteItem record)
    {
        return toAjax(baseVoteItemService.updateByPrimaryKeySelective(record));
    }





}
