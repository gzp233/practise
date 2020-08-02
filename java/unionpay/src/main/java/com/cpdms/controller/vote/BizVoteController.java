package com.cpdms.controller.vote;

import com.cpdms.common.base.BaseController;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.domain.AjaxResult;
import com.cpdms.model.custom.TableSplitResult;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.custom.TitleVo;
import com.cpdms.model.vote.BizVote;
import com.cpdms.service.vote.BizVoteService;
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
@RequestMapping("BizVoteController")
public class BizVoteController extends BaseController {

	private String prefix = "vote/bizVote";
	@Autowired
	private BizVoteService bizVoteService;

	@GetMapping("view")
	@RequiresPermissions("vote:bizVote:view")
    public String view(ModelMap model)
    {
		String str="";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }

	//@Log(title = "集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("vote:bizVote:list")
	@ResponseBody
	public Object list(Tablepar tablepar, String searchTxt){
		PageInfo<BizVote> page=bizVoteService.list(tablepar,searchTxt) ;
		TableSplitResult<BizVote> result=new TableSplitResult<BizVote>(page.getPageNum(), page.getTotal(), page.getList());
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
	@RequiresPermissions("vote:bizVote:add")
	@ResponseBody
	public AjaxResult add(BizVote bizVote){
		int b=bizVoteService.insertSelective(bizVote);
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
	@RequiresPermissions("vote:bizVote:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=bizVoteService.deleteByPrimaryKey(ids);
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
	public int checkNameUnique(BizVote bizVote){
		int b=bizVoteService.checkNameUnique(bizVote);
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
        mmap.put("BizVote", bizVoteService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }

	/**
     * 修改保存
     */
    //@Log(title = "修改", action = "111")
    @RequiresPermissions("vote:bizVote:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(BizVote record)
    {
        return toAjax(bizVoteService.updateByPrimaryKeySelective(record));
    }





}
