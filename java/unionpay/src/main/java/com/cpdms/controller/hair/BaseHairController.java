package com.cpdms.controller.hair;

import java.util.HashMap;
import java.util.List;

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
import com.cpdms.model.dinning.ImgUrl;
import com.cpdms.model.hair.BaseHair;
import com.cpdms.model.hair.BaseHairSrv;
import com.cpdms.service.dinning.ImgUrlService;
import com.cpdms.service.hair.BaseHairService;
import com.cpdms.service.hair.BaseHairSrvService;
import com.github.pagehelper.PageInfo;

@Controller
@RequestMapping("BaseHairController")

public class BaseHairController extends BaseController {

    private String prefix = "hair/baseHair";
    @Autowired
    private BaseHairService baseHairService;
    @Autowired
	private BaseHairSrvService baseHairSrvService;
    @Autowired
    private ImgUrlService imgUrlService;

    @GetMapping("view")
    @RequiresPermissions("hair:baseHair:view")
    public String view(ModelMap model) {
        String str = "美发项目";
        setTitle(model, new TitleVo("列表", str + "管理", true, "欢迎进入" + str + "页面", true, false));
        return prefix + "/list";
    }

    //@Log(title = "美发项目表集合查询", action = "111")
    @PostMapping("list")
    @RequiresPermissions("hair:baseHair:list")
    @ResponseBody
    public Object list(Tablepar tablepar, String searchTxt) {
        BaseHair baseHair = new BaseHair();
        baseHair.setHairName(searchTxt);
        PageInfo<BaseHair> page = baseHairService.getHairAndImgs(tablepar, baseHair);
        TableSplitResult<BaseHair> result = new TableSplitResult<BaseHair>(page.getPageNum(), page.getTotal(), page.getList());
        return result;
    }

    @PostMapping(value = "listJson",produces="application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public Object listJson(@RequestBody HashMap<String, Object> params) {
        Integer pageNum = (Integer)params.get("pageNum");
		Integer pageSize = (Integer)params.get("pageSize");
		Tablepar tablepar = new Tablepar();
		tablepar.setPageNum(pageNum);
		tablepar.setPageSize(pageSize);
		BaseHair baseHair = new BaseHair();

		if (params.get("hairType") != null) {
		    baseHair.setHairType(Integer.getInteger(params.get("hairType").toString()));
        }
        if (params.get("serviceId") != null) {
            baseHair.setServiceId(params.get("serviceId").toString());
        }

        PageInfo<BaseHair> page = baseHairService.getHairAndImgs(tablepar, baseHair);
        TableSplitResult<BaseHair> result = new TableSplitResult<BaseHair>(page.getPageNum(), page.getTotal(), page.getList());
        return result;
    }

    /**
     * 新增
     */

    @GetMapping("/add")
    public String add(ModelMap modelMap) {
        //服务类型
    	List<BaseHairSrv> baseHairSrvList = baseHairSrvService.all();
    	modelMap.put("baseHairSrvList",baseHairSrvList);

        return prefix + "/add";
    }

    //@Log(title = "美发项目表新增", action = "111")
    @PostMapping("add")
    @RequiresPermissions("hair:baseHair:add")
    @ResponseBody
    public AjaxResult add(BaseHair baseHair) {
        ImgUrl img = new ImgUrl();
        String oldImgId = baseHair.getImgId();
        int b = baseHairService.insertSelective(baseHair);
        if (b <= 0) {
            return error();
        }
        //更新图片
        img.setImgId(baseHair.getImgId());
        img.setId(oldImgId);
        int a = imgUrlService.updateByPrimaryKeySelective(img);
        if (a <= 0) {
            return error();
        }
        return success();
    }

    /**
     * 删除用户
     *
     * @param ids
     * @return
     */
    //@Log(title = "美发项目表删除", action = "111")
    @PostMapping("remove")
    @RequiresPermissions("hair:baseHair:remove")
    @ResponseBody
    public AjaxResult remove(String ids) {
        int b = baseHairService.deleteByPrimaryKey(ids);
        if (b > 0) {
            return success();
        } else {
            return error();
        }
    }

    /**
     * 检查用户
     *
     * @param
     * @return
     */
    @PostMapping("checkNameUnique")
    @ResponseBody
    public int checkNameUnique(BaseHair baseHair) {
        int b = baseHairService.checkNameUnique(baseHair);
        if (b > 0) {
            return 1;
        } else {
            return 0;
        }
    }


    /**
     * 修改跳转
     *
     * @param id
     * @param mmap
     * @return
     */
    @GetMapping("/edit/{id}")
    public String edit(@PathVariable("id") String id, ModelMap mmap) {
		BaseHair baseHair = baseHairService.selectOne(id);
		//服务类型
    	List<BaseHairSrv> baseHairSrvList = baseHairSrvService.all();

    	mmap.put("baseHairSrvList",baseHairSrvList);
        mmap.put("BaseHair", baseHair);

        return prefix + "/edit";
    }

    /**
     * 修改保存
     */
    //@Log(title = "美发项目表修改", action = "111")
    @RequiresPermissions("hair:baseHair:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(BaseHair baseHair) {
        ImgUrl img = new ImgUrl();
		String oldImgId = baseHair.getImgId();
		int b=baseHairService.updateByPrimaryKeySelective(baseHair);
		if(b<=0){
			return error();
		}
		//更新图片
		img.setImgId(baseHair.getImgId());
		img.setId(oldImgId);
		int a = imgUrlService.updateByPrimaryKeySelective(img);
		if(a <= 0){
			return error();
		}

		return success();
    }


}
