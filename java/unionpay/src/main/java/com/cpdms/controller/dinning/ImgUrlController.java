package com.cpdms.controller.dinning;

import org.apache.shiro.authz.annotation.RequiresPermissions;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.ModelMap;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.ResponseBody;
import org.springframework.web.multipart.MultipartFile;

import com.cpdms.common.base.BaseController;
import com.cpdms.common.domain.AjaxResult;
import com.cpdms.model.custom.TableSplitResult;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.custom.TitleVo;
import com.cpdms.model.dinning.ImgUrl;
import com.cpdms.service.dinning.ImgUrlService;
import com.github.pagehelper.PageInfo;

@Controller
@RequestMapping("ImgUrlController")

public class ImgUrlController extends BaseController{
	
	private String prefix = "dinning/imgUrl";
	@Autowired
	private ImgUrlService imgUrlService;
	
	@GetMapping("view")
	@RequiresPermissions("dinning:imgUrl:view")
    public String view(ModelMap model)
    {	
		String str="图片路径";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }
	
	//@Log(title = "图片路径集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("dinning:imgUrl:list")
	@ResponseBody
	public Object list(Tablepar tablepar,String searchTxt){
		PageInfo<ImgUrl> page=imgUrlService.list(tablepar,searchTxt) ; 
		TableSplitResult<ImgUrl> result=new TableSplitResult<ImgUrl>(page.getPageNum(), page.getTotal(), page.getList()); 
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
	
	//@Log(title = "图片路径新增", action = "111")
	@PostMapping("add")
	@RequiresPermissions("dinning:imgUrl:add")
	@ResponseBody
	public AjaxResult add(ImgUrl imgUrl){
		int b=imgUrlService.insertSelective(imgUrl);
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
	//@Log(title = "图片路径删除", action = "111")
	@PostMapping("remove")
	@RequiresPermissions("dinning:imgUrl:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=imgUrlService.deleteByPrimaryKey(ids);
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
	public int checkNameUnique(ImgUrl imgUrl){
		/*
		 * int b=imgUrlService.checkNameUnique(imgUrl); if(b>0){ return 1; }else{ return
		 * 0; }
		 */
		return 0;
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
        mmap.put("ImgUrl", imgUrlService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }
	
	/**
     * 修改保存
     */
    //@Log(title = "图片路径修改", action = "111")
    @RequiresPermissions("dinning:imgUrl:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(ImgUrl record)
    {
        return toAjax(imgUrlService.updateByPrimaryKeySelective(record));
    }

    /**
     * 上传文件
     */
    @PostMapping("/upload")
    @ResponseBody
    public AjaxResult updateImg(@RequestParam("file") MultipartFile file)
    {
    	 try
         {
             if (!file.isEmpty())
             {
                 //插入文件存储表
             	String id=imgUrlService.insertSelective(file);
                 if(id!=null){
                 	 return AjaxResult.successData(200, id);
                 }
             }
             return error();
         }
         catch (Exception e)
         {
             return error(e.getMessage());
         }
       
    }

	/**
	 * 上传图片
	 */
	@PostMapping("/upload2")
	@ResponseBody
	public AjaxResult updateImg2(@RequestParam("file") MultipartFile file)
	{
		try
		{
			if (!file.isEmpty())
			{
				//插入文件存储表
				String id=imgUrlService.insertSelective2(file);
				if(id!=null){
					return AjaxResult.successData(200, id);
				}
			}
			return error();
		}
		catch (Exception e)
		{
			return error(e.getMessage());
		}

	}

	/**
	 * 上传文件
	 */
	@PostMapping("/upload3")
	@ResponseBody
	public AjaxResult uploadFile3(@RequestParam("file") MultipartFile file)
	{
		if (!file.getOriginalFilename().equals("bankerInfos.zip")) {
			return error("文件名错误");
		}
		try
		{
			if (!file.isEmpty())
			{
				//插入文件存储表
				String id=imgUrlService.insertSelective3(file);
				if(id!=null){
					return AjaxResult.successData(200, id);
				}
			}
			return error();
		}
		catch (Exception e)
		{
			return error(e.getMessage());
		}

	}
    
    /**
	 * 删除本地文件
	 * @param ids
	 * @return
	 */
	@PostMapping("del_file")
	@ResponseBody
	public AjaxResult del_file(String ids){
		int b=imgUrlService.deleteBydataFile(ids);
		if(b>0){
			return success();
		}else{
			return error();
		}
	}
	
}
