package com.cpdms.controller.dinning;

import java.util.HashMap;

import com.cpdms.common.token.ApiTokenValid;
import org.apache.shiro.authz.annotation.RequiresPermissions;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.ModelMap;
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
import com.cpdms.model.dinning.BaseCook;
import com.cpdms.model.dinning.ImgUrl;
import com.cpdms.service.dinning.BaseCookService;
import com.cpdms.service.dinning.ImgUrlService;
import com.github.pagehelper.PageInfo;

@Controller
@RequestMapping("BaseCookController")

public class BaseCookController extends BaseController {

	private String prefix = "dinning/baseCook";
	@Autowired
	private BaseCookService baseCookService;
	@Autowired
    private ImgUrlService imgUrlService;

	@GetMapping("view")
	@RequiresPermissions("dinning:baseCook:view")
    public String view(ModelMap model)
    {
		String str="厨师";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }

	//@Log(title = "厨师表集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("dinning:baseCook:list")
	@ResponseBody
	public Object list(Tablepar tablepar,String searchTxt){
		PageInfo<BaseCook> page=baseCookService.getCookAndImgs(tablepar,searchTxt) ;
		TableSplitResult<BaseCook> result=new TableSplitResult<BaseCook>(page.getPageNum(), page.getTotal(), page.getList());
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
		String cookName = "";
		if (params.get("cookName") != null) {
		    cookName = params.get("cookName").toString();
        }
		PageInfo<BaseCook> page=baseCookService.getCookAndImgs(tablepar,cookName) ;
		TableSplitResult<BaseCook> result=new TableSplitResult<BaseCook>(page.getPageNum(), page.getTotal(), page.getList());
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

	//@Log(title = "厨师表新增", action = "111")
	@PostMapping("add")
	@RequiresPermissions("dinning:baseCook:add")
	@ResponseBody
	public AjaxResult add(BaseCook baseCook){
        ImgUrl img = new ImgUrl();
		String oldImgId = baseCook.getImgId();
		int b=baseCookService.insertSelective(baseCook);
		if(b<=0){
			return error();
		}
		//更新图片
		img.setImgId(baseCook.getImgId());
		img.setId(oldImgId);
		int a = imgUrlService.updateByPrimaryKeySelective(img);
		if(a <= 0){
			return error();
		}

		return success();
	}

	/**
	 * 删除用户
	 * @param ids
	 * @return
	 */
	//@Log(title = "厨师表删除", action = "111")
	@PostMapping("remove")
	@RequiresPermissions("dinning:baseCook:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=baseCookService.deleteByPrimaryKey(ids);
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
	public int checkNameUnique(BaseCook baseCook){
		int b=baseCookService.checkNameUnique(baseCook);
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
        mmap.put("BaseCook", baseCookService.selectOne(id));

        return prefix + "/edit";
    }

	/**
     * 修改保存
     */
    //@Log(title = "厨师表修改", action = "111")
    @RequiresPermissions("dinning:baseCook:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(BaseCook baseCook)
    {
        ImgUrl img = new ImgUrl();
		String oldImgId = baseCook.getImgId();
		int b=baseCookService.updateByPrimaryKeySelective(baseCook);
		if(b<=0){
			return error();
		}
		//更新图片
		img.setImgId(baseCook.getImgId());
		img.setId(oldImgId);
		int a = imgUrlService.updateByPrimaryKeySelective(img);
		if(a <= 0){
			return error();
		}

		return success();
    }





}
