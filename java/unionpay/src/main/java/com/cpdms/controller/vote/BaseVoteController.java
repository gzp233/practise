package com.cpdms.controller.vote;

import cn.hutool.json.JSONUtil;
import com.cpdms.common.base.BaseController;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.domain.AjaxResult;
import com.cpdms.common.token.ApiTokenValid;
import com.cpdms.mapper.vote.BaseVoteItemMapper;
import com.cpdms.mapper.vote.BaseVoteMapper;
import com.cpdms.mapper.vote.BizVoteMapper;
import com.cpdms.model.custom.TableSplitResult;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.custom.TitleVo;
import com.cpdms.model.vote.*;
import com.cpdms.service.vote.BaseVoteService;
import com.cpdms.util.SnowflakeIdWorker;
import org.apache.shiro.authz.annotation.RequiresPermissions;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.ModelMap;
import org.springframework.web.bind.annotation.*;

import java.util.*;

@Controller
@RequestMapping("BaseVoteController")
public class BaseVoteController extends BaseController {

	private String prefix = "vote/baseVote";
	@Autowired
	private BaseVoteService baseVoteService;
	@Autowired
    private BizVoteMapper bizVoteMapper;
	@Autowired
    private BaseVoteItemMapper baseVoteItemMapper;
	@Autowired
    private BaseVoteMapper baseVoteMapper;

	@GetMapping("view")
	@RequiresPermissions("vote:baseVote:view")
    public String view(ModelMap model)
    {
		String str="";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }

	//@Log(title = "集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("vote:baseVote:list")
	@ResponseBody
	public Object list(Tablepar tablepar, String searchTxt){
	    BaseVoteExample testExample = new BaseVoteExample();
	    if(searchTxt!=null && !"".equals(searchTxt)){
            testExample.createCriteria().andVoteNameLike("%"+searchTxt+"%");
        }
        testExample.setOrderByClause("create_date desc");
        testExample.createCriteria().andStatusEqualTo(1);

		PageInfo<BaseVote> page=baseVoteService.list(tablepar,testExample) ;
		TableSplitResult<BaseVote> result=new TableSplitResult<BaseVote>(page.getPageNum(), page.getTotal(), page.getList());
		return  result;
	}

	@PostMapping(value = "listJson", produces = "application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public Object listJson(@RequestBody HashMap<String, Object> params) {
        Integer pageNum = (Integer) params.get("pageNum");
        Integer pageSize = (Integer) params.get("pageSize");
        if (params.get("voteStatus") == null) {
            return error(402, "投票状态不能为空");
        }
        String voteStatus = params.get("voteStatus").toString();
        Tablepar tablepar = new Tablepar();
        tablepar.setPageNum(pageNum);
        tablepar.setPageSize(pageSize);
        BaseVoteExample testExample = new BaseVoteExample();
        testExample.createCriteria().andVoteStatusEqualTo(voteStatus).andStatusEqualTo(1);
        testExample.setOrderByClause("create_date desc");
        PageInfo<BaseVote> page=baseVoteService.list(tablepar,testExample) ;
		TableSplitResult<BaseVote> result=new TableSplitResult<BaseVote>(page.getPageNum(), page.getTotal(), page.getList());

		return  result;
    }

    @PostMapping(value = "getOne", produces = "application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public Object getOne(@RequestBody HashMap<String, Object> params) {
        String id = params.get("id").toString();
        String cardNo = params.get("cardNo").toString();

        BaseVote baseVote = baseVoteService.selectByPrimaryKey(id);
        List<String> itemIds = new ArrayList<>();
        for (BaseVoteItem baseVoteItem : baseVote.getBaseVoteItemList()) {
            itemIds.add(baseVoteItem.getId());
        }
        BizVoteExample bizVoteExample = new BizVoteExample();
        bizVoteExample.createCriteria().andCardNoEqualTo(cardNo).andItemIdIn(itemIds);
        List<BizVote> bizVotes  = bizVoteMapper.selectByExample(bizVoteExample);
        Map<String, Object> resultMap = new HashMap<>();
        resultMap.put("baseVote", baseVote);
        resultMap.put("bizVotes", bizVotes);

        return retobject(200, resultMap);
    }

    @PostMapping(value = "vote", produces = "application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public Object vote(@RequestBody HashMap<String, Object> params) {
        String id = params.get("id").toString();
        String cardNo = params.get("cardNo").toString();
        List<String> itemIdList = JSONUtil.parseArray(params.get("itemIdList")).toList(String.class);

        BaseVote baseVote = baseVoteService.selectByPrimaryKey(id);
        if (baseVote == null || baseVote.getStatus() == 0) {
            return error(402, "投票不存在");
        }
        if (baseVote.getEndTime().before(new Date())) {
            return error(402, "投票已结束");
        }
        if (baseVote.getVoteType() == 0 && itemIdList.size() > 1) {
            return error(402, "单选只能选择一个");
        }
        List<String> itemIds = new ArrayList<>();
        for (BaseVoteItem baseVoteItem : baseVote.getBaseVoteItemList()) {
            itemIds.add(baseVoteItem.getId());
        }
        // 先删掉以前的投票
        BizVoteExample bizVoteExample = new BizVoteExample();
        bizVoteExample.createCriteria().andItemIdIn(itemIds).andCardNoEqualTo(cardNo);
        List<BizVote> bizVotes = bizVoteMapper.selectByExample(bizVoteExample);
        if (bizVotes.size() > 0) {
            List<String> oldIds = new ArrayList<>();
            for (BizVote bizVote:bizVotes) {
                oldIds.add(bizVote.getItemId());
            }
            baseVoteItemMapper.decreaseNum(oldIds);
            Map<String, Object> delMap = new HashMap<String, Object>();
            delMap.put("cardNo", cardNo);
            delMap.put("itemIds", itemIds);
            bizVoteMapper.deleteByCardNoAndItemId(delMap);
            baseVoteMapper.decreaseNum(baseVote.getId());
        }

        // 插入新的表
        baseVoteMapper.increaseNum(baseVote.getId());
        baseVoteItemMapper.increaseNum(itemIdList);
        for (String itemId : itemIdList) {
            BizVote bizVote = new BizVote();
            bizVote.setId(SnowflakeIdWorker.getUUID());
            bizVote.setCardNo(cardNo);
            bizVote.setItemId(itemId);
            bizVoteMapper.insertSelective(bizVote);
        }

        return retobject(200, "");
    }

	/**
	 * 详情跳转
	 * @param id
	 * @param mmap
	 * @return
	 */
	@GetMapping("/detail/{id}")
    public String detail(@PathVariable("id") String id, ModelMap mmap)
    {
        BaseVote baseVote = baseVoteService.selectByPrimaryKey(id);
        mmap.put("BaseVote", baseVote);
        // 统计一下总票数,计算百分比
        Integer total = 0;
        for (BaseVoteItem baseVoteItem: baseVote.getBaseVoteItemList()) {
            total += baseVoteItem.getNum();
        }

        // 设置百分比
        for (BaseVoteItem baseVoteItem: baseVote.getBaseVoteItemList()) {
            String percent = "";
            if (total == 0) {
                percent = "0.00%";
            } else {
                percent = String.format("%.2f%%", new Double(baseVoteItem.getNum() * 100 / total));
            }

            baseVoteItem.setPercent(percent);
        }

        return prefix + "/detail";
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
	@RequiresPermissions("vote:baseVote:add")
	@ResponseBody
	public AjaxResult add(BaseVote baseVote, @RequestParam(value="voteItems") List<String> voteItems){
        if (baseVote.getEndTime() == null || baseVote.getEndTime().before(new Date())) {
            return error("截止时间错误");
        }
        if (voteItems.size() == 0) {
            return error("选项不能为空");
        }

        Set set = new HashSet<String>();
        for (int i = 0; i<voteItems.size(); i++) {
            set.add(voteItems.toArray()[i]);
        }
        if (set.size() != voteItems.size()) {
            return error("选项不能重复");
        }

		int b=baseVoteService.saveVote(baseVote, voteItems);
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
	@RequiresPermissions("vote:baseVote:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=baseVoteService.deleteByPrimaryKey(ids);
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
	public int checkNameUnique(BaseVote baseVote){
		int b=baseVoteService.checkNameUnique(baseVote);
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
        mmap.put("BaseVote", baseVoteService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }

	/**
     * 修改保存
     */
    //@Log(title = "修改", action = "111")
    @RequiresPermissions("vote:baseVote:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(BaseVote record)
    {
        return toAjax(baseVoteService.updateByPrimaryKeySelective(record));
    }





}
