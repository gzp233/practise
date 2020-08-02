package com.cpdms.controller.dinning;

import com.cpdms.common.base.BaseController;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.domain.AjaxResult;
import com.cpdms.model.custom.TableSplitResult;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.custom.TitleVo;
import com.cpdms.model.dinning.BizOrdPay;
import com.cpdms.service.dinning.BizOrdPayService;
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
@RequestMapping("BizOrdPayController")
public class BizOrdPayController extends BaseController {
	
	private String prefix = "dinning/bizOrdPay";
	@Autowired
	private BizOrdPayService bizOrdPayService;
	
	@GetMapping("view")
	@RequiresPermissions("dinning:bizOrdPay:view")
    public String view(ModelMap model)
    {	
		String str="订单支付";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }
	
	//@Log(title = "订单支付集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("dinning:bizOrdPay:list")
	@ResponseBody
	public Object list(Tablepar tablepar, String searchTxt){
		PageInfo<BizOrdPay> page=bizOrdPayService.list(tablepar,searchTxt) ;
		TableSplitResult<BizOrdPay> result=new TableSplitResult<BizOrdPay>(page.getPageNum(), page.getTotal(), page.getList());
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
	
	//@Log(title = "订单支付新增", action = "111")
	@PostMapping("add")
	@RequiresPermissions("dinning:bizOrdPay:add")
	@ResponseBody
	public AjaxResult add(BizOrdPay bizOrdPay){
		int b=bizOrdPayService.insertSelective(bizOrdPay);
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
	//@Log(title = "订单支付删除", action = "111")
	@PostMapping("remove")
	@RequiresPermissions("dinning:bizOrdPay:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=bizOrdPayService.deleteByPrimaryKey(ids);
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
	public int checkNameUnique(BizOrdPay bizOrdPay){
		int b=bizOrdPayService.checkNameUnique(bizOrdPay);
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
        mmap.put("BizOrdPay", bizOrdPayService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }
	
	/**
     * 修改保存
     */
    //@Log(title = "订单支付修改", action = "111")
    @RequiresPermissions("dinning:bizOrdPay:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(BizOrdPay record)
    {
        return toAjax(bizOrdPayService.updateByPrimaryKeySelective(record));
    }

    
    

	
}
