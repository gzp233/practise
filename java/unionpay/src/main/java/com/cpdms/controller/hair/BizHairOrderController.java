package com.cpdms.controller.hair;

import java.util.Date;
import java.util.HashMap;
import java.util.List;

import javax.validation.Valid;

import com.cpdms.common.token.ApiTokenValid;
import com.cpdms.mapper.hair.BizHairOrderMapper;
import com.cpdms.model.hair.BaseHairSetting;
import com.cpdms.model.hair.BizHairOrderExample;
import com.cpdms.service.hair.BaseHairSettingService;
import com.cpdms.util.Constants;
import com.cpdms.util.DateUtils;
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
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.ResponseBody;

import com.cpdms.common.base.BaseController;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.domain.AjaxResult;
import com.cpdms.model.custom.TableSplitResult;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.custom.TitleVo;
import com.cpdms.model.hair.BizHairOrder;
import com.cpdms.service.hair.BizHairOrderService;

@Controller
@RequestMapping("BizHairOrderController")

public class BizHairOrderController extends BaseController {

    private String prefix = "hair/bizHairOrder";
    @Autowired
    private BizHairOrderService bizHairOrderService;

    @GetMapping("view")
    @RequiresPermissions("hair:bizHairOrder:view")
    public String view(ModelMap model) {
        String str = "美发预定订单表";
        setTitle(model, new TitleVo("列表", str + "管理", true, "欢迎进入" + str + "页面", true, false));
        return prefix + "/list";
    }

    //@Log(title = "美发预定订单表集合查询", action = "111")
    @PostMapping("list")
    @RequiresPermissions("hair:bizHairOrder:list")
    @ResponseBody
    public Object list(Tablepar tablepar, String searchTxt) {
        BizHairOrder bizHairOrder = new BizHairOrder();
        bizHairOrder.setOrdCode(searchTxt);
        PageInfo<BizHairOrder> page = bizHairOrderService.list(tablepar, bizHairOrder);
        TableSplitResult<BizHairOrder> result = new TableSplitResult<BizHairOrder>(page.getPageNum(), page.getTotal(), page.getList());
        return result;
    }

    @PostMapping(value = "listJson", produces = "application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public Object listJson(@RequestBody HashMap<String, Object> params) {
        Integer pageNum = (Integer) params.get("pageNum");
        Integer pageSize = (Integer) params.get("pageSize");
        Tablepar tablepar = new Tablepar();
        tablepar.setPageNum(pageNum);
        tablepar.setPageSize(pageSize);

        String cardNo = params.get("cardNo").toString();
        if (cardNo.equals("")) {
            return error(402, "卡号不能为空");
        }
        BizHairOrder bizHairOrder = new BizHairOrder();
        bizHairOrder.setCardNo(cardNo);
        bizHairOrder.setStatus(1);
        PageInfo<BizHairOrder> page = bizHairOrderService.list(tablepar, bizHairOrder);
        TableSplitResult<BizHairOrder> result = new TableSplitResult<BizHairOrder>(page.getPageNum(), page.getTotal(), page.getList());
        return result;
    }

    @PostMapping(value = "canOrder", produces = "application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public Object canOrder(@RequestBody HashMap<String, Object> params) {
        String dateStr = params.get("date").toString();
        Date date = DateUtils.parseDate(dateStr);
        return retobject(200, bizHairOrderService.canOrder(date));
    }

    @PostMapping(value = "getOrderCount", produces = "application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public Object getOrderCount(@RequestBody HashMap<String, Object> params) {
        String dateStr = params.get("date").toString();
        Date date = DateUtils.parseDate(dateStr);
        HashMap<String, Integer> map = bizHairOrderService.getOrderStatusByTime(date);
        return retobject(200, map);
    }

    @PostMapping(value = "getSettingNum", produces = "application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public Object getSettingNum(@RequestBody HashMap<String, Object> params) {
        String dateStr = params.get("date").toString();
        Date date = DateUtils.parseDate(dateStr);
        Integer num = bizHairOrderService.getSettingNum(date);
        return retobject(200, num);
    }

    @PostMapping(value = "getOne", produces = "application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public Object getOne(@RequestBody HashMap<String, Object> params) {
        String id = params.get("id").toString();
        if (id == null || id.equals("")) {
            return error(402, "ID不能为空");
        }
        return retobject(200, bizHairOrderService.selectByPrimaryKey(id));
    }

    //  获取订单详情
    @PostMapping(value = "ordDetail")
    @ResponseBody
    public AjaxResult ordDetail(@RequestParam(value = "id", required = true) String id) {
        return retobject(200, bizHairOrderService.selectByPrimaryKey(id));

    }

    //  确认订单
    @PostMapping(value = "confirm")
    @ResponseBody
    public AjaxResult confirm(@RequestParam(value = "id", required = true) String id) {
        BizHairOrder bizHairOrder = new BizHairOrder();
        bizHairOrder.setOrdState(Constants.HAIR_ORD_FINISHED);
        bizHairOrder.setFinishTime(new Date());
        bizHairOrder.setUpdateDate(new Date());
        bizHairOrder.setId(id);
        int b = bizHairOrderService.updateByPrimaryKeySelective(bizHairOrder);
        if (b <= 0) {
            return error(402, "确认失败");
        }
        return retobject(200, "确认成功");

    }

    /**
     * 新增
     */

    @GetMapping("/add")
    public String add(ModelMap modelMap) {
        return prefix + "/add";
    }

    //@Log(title = "美发预定订单表新增", action = "111")
    @PostMapping(value = "add", produces = "application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public AjaxResult add(@RequestBody @Valid BizHairOrder bizHairOrder, BindingResult result) {
        if (result.hasErrors()) {
            List<ObjectError> list = result.getAllErrors();
            for (ObjectError error : list) {
                return error(402, error.getDefaultMessage());
            }
        }
        if (bizHairOrder.getOrdTime().before(new Date())) {
            return error(402, "预约时间不能小于当前时间");
        }

        HashMap<String, String> b = bizHairOrderService.saveOrder(bizHairOrder);
        if (!b.get("code").equals("0")) {
            return error(402, b.get("msg"));
        }

        return retobject(200, b.get("msg"));
    }

    /**
     * 删除用户
     *
     * @param ids
     * @return
     */
    //@Log(title = "美发预定订单表删除", action = "111")
    @PostMapping("remove")
    @RequiresPermissions("hair:bizHairOrder:remove")
    @ResponseBody
    public AjaxResult remove(String ids) {
        int b = bizHairOrderService.deleteByPrimaryKey(ids);
        if (b > 0) {
            return success();
        } else {
            return error();
        }
    }

    /**
     * 检查用户
     */
    @PostMapping("checkNameUnique")
    @ResponseBody
    public int checkNameUnique(BizHairOrder bizHairOrder) {
        int b = bizHairOrderService.checkNameUnique(bizHairOrder);
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
        mmap.put("BizHairOrder", bizHairOrderService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }

    /**
     * 修改保存
     */
    //@Log(title = "美发预定订单表修改", action = "111")
    @PostMapping(value = "/edit", produces = "application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public AjaxResult editSave(@RequestBody BizHairOrder record) {
        return toAjax(bizHairOrderService.updateByPrimaryKeySelective(record));
    }


}
