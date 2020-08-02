package com.cpdms.controller.payment;

import com.cpdms.common.base.BaseController;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.domain.AjaxResult;
import com.cpdms.model.custom.TableSplitResult;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.custom.TitleVo;
import com.cpdms.model.payment.BizPayment;
import com.cpdms.service.payment.BizPaymentService;
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
@RequestMapping("BizPaymentController")
public class BizPaymentController extends BaseController {
	
	private String prefix = "payment/bizPayment";
	@Autowired
	private BizPaymentService bizPaymentService;
	
	@GetMapping("view")
	@RequiresPermissions("payment:bizPayment:view")
    public String view(ModelMap model)
    {	
		String str="交易记录表";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }
	
	//@Log(title = "交易记录表集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("payment:bizPayment:list")
	@ResponseBody
	public Object list(Tablepar tablepar, String searchTxt){
		PageInfo<BizPayment> page=bizPaymentService.list(tablepar,searchTxt) ;
		TableSplitResult<BizPayment> result=new TableSplitResult<BizPayment>(page.getPageNum(), page.getTotal(), page.getList());
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
	
	//@Log(title = "交易记录表新增", action = "111")
	@PostMapping("add")
	@RequiresPermissions("payment:bizPayment:add")
	@ResponseBody
	public AjaxResult add(BizPayment bizPayment){
		int b=bizPaymentService.insertSelective(bizPayment);
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
	//@Log(title = "交易记录表删除", action = "111")
	@PostMapping("remove")
	@RequiresPermissions("payment:bizPayment:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=bizPaymentService.deleteByPrimaryKey(ids);
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
	public int checkNameUnique(BizPayment bizPayment){
		int b=bizPaymentService.checkNameUnique(bizPayment);
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
        mmap.put("BizPayment", bizPaymentService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }
	
	/**
     * 修改保存
     */
    //@Log(title = "交易记录表修改", action = "111")
    @RequiresPermissions("payment:bizPayment:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(BizPayment record)
    {
        return toAjax(bizPaymentService.updateByPrimaryKeySelective(record));
    }

    
    

	
}
