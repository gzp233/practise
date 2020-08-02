package com.cpdms.controller.dinning;

import java.util.Date;
import java.util.HashMap;
import java.util.List;

import javax.validation.Valid;

import com.cpdms.common.token.ApiTokenValid;
import org.apache.shiro.authz.annotation.RequiresPermissions;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.ModelMap;
import org.springframework.validation.BindingResult;
import org.springframework.validation.ObjectError;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.ResponseBody;

import com.cpdms.common.base.BaseController;
import com.cpdms.common.domain.AjaxResult;
import com.cpdms.model.custom.TableSplitResult;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.custom.TitleVo;
import com.cpdms.model.dinning.Appraise;
import com.cpdms.service.dinning.AppraiseService;
import com.github.pagehelper.PageInfo;

@Controller
@RequestMapping("AppraiseController")

public class AppraiseController extends BaseController{

	private String prefix = "dinning/appraise";
	@Autowired
	private AppraiseService appraiseService;

	@GetMapping("view")
	@RequiresPermissions("dinning:appraise:view")
    public String view(ModelMap model)
    {
		String str="评价表";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }

	//@Log(title = "评价表集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("dinning:appraise:list")
	@ResponseBody
	public Object list(Tablepar tablepar,String searchTxt){
		PageInfo<Appraise> page=appraiseService.listRelative(tablepar,searchTxt) ;
		TableSplitResult<Appraise> result=new TableSplitResult<Appraise>(page.getPageNum(), page.getTotal(), page.getList());
		return  result;
	}

	@PostMapping(value = "listJson", produces="application/json;charset=utf-8")
	@ResponseBody
	@ApiTokenValid
    public Object listJson(@RequestBody HashMap<String, Object> params){
	    Integer pageNum = (Integer)params.get("pageNum");
		Integer pageSize = (Integer)params.get("pageSize");
		Tablepar tablepar = new Tablepar();
		tablepar.setPageNum(pageNum);
		tablepar.setPageSize(pageSize);
		String foodId = "";
		if (params.get("foodId") != null) {
		    foodId = params.get("foodId").toString();
        } else {
		    return error(402, "菜品ID不能为空");
        }
		PageInfo<Appraise> page=appraiseService.list(tablepar,foodId) ;
		TableSplitResult<Appraise> result=new TableSplitResult<Appraise>(page.getPageNum(), page.getTotal(), page.getList());
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

	//@Log(title = "评价表新增", action = "111")
	@PostMapping(value = "add")
	@RequiresPermissions("dinning:appraise:add")
	@ResponseBody
	public AjaxResult add(Appraise appraise){
		int b=appraiseService.insertSelective(appraise);
		if(b>0){
			return success();
		}else{
			return error();
		}
	}

	@PostMapping(value = "addJson", produces="application/json;charset=utf-8")
	@ResponseBody
	@ApiTokenValid
	public AjaxResult addJson(@RequestBody @Valid Appraise appraise , BindingResult result){
        if (result.hasErrors()) {
            List<ObjectError> list = result.getAllErrors();
			for (ObjectError error : list) {
				return retobject(402, error.getDefaultMessage());
			}
        }
        if (appraise.getSource().equals("order") && (appraise.getOrdId() == null || appraise.getOrdId().equals(""))) {
            return retobject(200, "订单ID不能为空");
        }
        appraise.setCreateTime(new Date());
        if (!appraiseService.canAppraise(appraise)) {
            return error("评价次数达到限制");
        }
		int b=appraiseService.insertSelective(appraise);
		if(b>0){
			return retobject(200, "评价成功");
		}else{
			return error(402, "评价失败");
		}
	}

	@PostMapping(value = "canAppraise", produces="application/json;charset=utf-8")
	@ResponseBody
	@ApiTokenValid
	public AjaxResult canAppraise(@RequestBody HashMap<String, Object> params){
        Appraise appraise = new Appraise();
        if (params.get("source") == null) {
            return error(402, "评价来源不能为空");
        }
        String source = params.get("source").toString();
        if (!source.equals("daily") && !source.equals("order")) {
            return error(402, "评价来源错误");
        }
        String cardNo = params.get("cardNo").toString();
        String foodId = params.get("foodId").toString();
        String ordId = "";
        if (source.equals("order")) {
            if (params.get("ordId") == null) {
                 return error(402, "订单ID不能为空");
            }
            ordId = params.get("ordId").toString();
        }
        appraise.setSource(source);
        appraise.setCardNo(cardNo);
        appraise.setOrdId(ordId);
        appraise.setFoodId(foodId);

        return retobject(200, appraiseService.canAppraise(appraise));
	}

	/**
	 * 删除用户
	 * @param ids
	 * @return
	 */
	//@Log(title = "评价表删除", action = "111")
	@PostMapping("remove")
	@RequiresPermissions("dinning:appraise:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=appraiseService.deleteByPrimaryKey(ids);
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
	public int checkNameUnique(Appraise appraise){
		int b=appraiseService.checkNameUnique(appraise);
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
        mmap.put("Appraise", appraiseService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }

	/**
     * 修改保存
     */
    //@Log(title = "评价表修改", action = "111")
    @RequiresPermissions("dinning:appraise:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(Appraise record)
    {
        return toAjax(appraiseService.updateByPrimaryKeySelective(record));
    }





}
