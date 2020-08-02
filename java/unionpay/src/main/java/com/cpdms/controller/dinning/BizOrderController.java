package com.cpdms.controller.dinning;


import java.math.BigDecimal;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

import javax.validation.Valid;

import com.cpdms.common.token.ApiTokenValid;
import cn.hutool.json.JSONObject;
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
import com.cpdms.common.domain.AjaxResult;
import com.cpdms.model.custom.TableSplitResult;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.custom.TitleVo;
import com.cpdms.model.dinning.BaseTimeRange;
import com.cpdms.model.dinning.BizOrder;
import com.cpdms.service.dinning.BaseTimeRangeService;
import com.cpdms.service.dinning.BizOrderService;
import com.github.pagehelper.PageHelper;
import com.github.pagehelper.PageInfo;

import cn.hutool.json.JSONUtil;


@Controller
@RequestMapping("BizOrderController")

public class BizOrderController extends BaseController {

    private String prefix = "dinning/bizOrder";
    @Autowired
    private BizOrderService bizOrderService;
    @Autowired
    private BaseTimeRangeService baseTimeRangeService;

    @GetMapping("view")
    @RequiresPermissions("dinning:bizOrder:view")
    public String view(ModelMap model) {
        String str = "预定订单";
        setTitle(model, new TitleVo("列表", str + "管理", true, "欢迎进入" + str + "页面", true, false));
        return prefix + "/list";
    }

    //@Log(title = "预定订单集合查询", action = "111")
    @PostMapping("list")
    @RequiresPermissions("dinning:bizOrder:list")
    @ResponseBody
    public Object list(Tablepar tablepar, String searchTxt) {
        PageInfo<BizOrder> page = bizOrderService.list(tablepar, searchTxt);
        TableSplitResult<BizOrder> result = new TableSplitResult<BizOrder>(page.getPageNum(), page.getTotal(), page.getList());
        return result;
    }

    //@Log(title = "预定订单集合查询", action = "111")
    @PostMapping(value = "getOrders", produces = "application/json;charset=utf-8")
//	@RequiresPermissions("dinning:bizOrder:getOrders")
    @ResponseBody
    @ApiTokenValid
    public Object getOrders(@RequestBody HashMap<String, Object> params) {
        Integer pageNum = (Integer) params.get("pageNum");
        Integer pageSize = (Integer) params.get("pageSize");
        Integer type = (Integer) params.get("type");
        String cardNo = params.get("cardNo").toString();
        String sellTypeName = "";
        if (params.get("sellTypeName") != null) {
            sellTypeName = params.get("sellTypeName").toString();
        }
        Tablepar tablepar = new Tablepar();
        tablepar.setPageNum(pageNum);
        tablepar.setPageSize(pageSize);
        if (type != 0 && type != 1) {
            return retobject(402, "type不能为空");
        }
        PageInfo<BizOrder> page = bizOrderService.getOrders(tablepar, type, cardNo, sellTypeName);
        TableSplitResult<BizOrder> result = new TableSplitResult<BizOrder>(page.getPageNum(), page.getTotal(), page.getList());
        return result;
    }

    @PostMapping(value = "getOne", produces = "application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public Object getOne(@RequestBody HashMap<String, Object> params) {
        String id = params.get("id").toString();
        if (id == null || id.equals("")) {
            return error(402, "ID不能为空");
        }
        return retobject(200, bizOrderService.selectByPrimaryKey(id));
    }


    @GetMapping("sumIndex")
    @RequiresPermissions("dinning:bizOrder:sumIndex")
    public String sumIndex(ModelMap model) {
        String str = "预定菜品处理";
        setTitle(model, new TitleVo("列表", str + "管理", true, "欢迎进入" + str + "页面", true, false));
        List<BaseTimeRange> baseTimeRangeList = baseTimeRangeService.getAll();
        model.put("baseTimeRangeList", baseTimeRangeList);
        return prefix + "/ordFoodSum";
    }

    //@Log(title = "预定处理", action = "ordFoodSum")
    @PostMapping(value = "ordFoodSum")
    @RequiresPermissions("dinning:bizOrder:ordFoodSum")
    @ResponseBody
    public Object ordFoodSum(Tablepar tablepar, @RequestParam(name = "ordTimeRangeId", required = false) String ordTimeRangeId,
                             @RequestParam(name = "plantedTakeTimeL", required = false) String plantedTakeTimeL, @RequestParam(name = "plantedTakeTimeG", required = false) String plantedTakeTimeG) {
        Map<String, Object> params = new HashMap<String, Object>();
        params.put("ordTimeRangeId", ordTimeRangeId);
        params.put("plantedTakeTimeL", plantedTakeTimeL);
        params.put("plantedTakeTimeG", plantedTakeTimeG);
        PageInfo<Map<String, Object>> page = bizOrderService.queryOrdFoodSumByTime(tablepar, params);
        TableSplitResult<Map<String, Object>> result = new TableSplitResult<Map<String, Object>>(page.getPageNum(), page.getTotal(), page.getList());

        //return  prefix + "/ordFoodSum";
        return result;

    }

    @GetMapping("ordPrtIdx")
    @RequiresPermissions("dinning:bizOrder:ordPrtIdx")
    public String ordPrtIdx(ModelMap model) {
        String str = "预定订单处理";
        setTitle(model, new TitleVo("列表", str + "管理", true, "欢迎进入" + str + "页面", true, false));
        List<BaseTimeRange> baseTimeRangeList = baseTimeRangeService.getAll();
        model.put("baseTimeRangeList", baseTimeRangeList);
        return prefix + "/ordPrtList";
    }

    //@Log(title = "预订菜品打印列表", action = "ordPrtList")
    @PostMapping(value = "ordPrtList")
    @RequiresPermissions("dinning:bizOrder:ordPrtList")
    @ResponseBody
    public Object ordPrtList(Tablepar tablepar, @RequestParam(name = "ordCode", required = false) String ordCode,@RequestParam(name = "ids[]", required = false) List<String> ids,
                             @RequestParam(name = "ordState", required = false) String ordState,@RequestParam(name = "ordTimeRangeId", required = false) String ordTimeRangeId,
                             @RequestParam(name = "plantedTakeTimeL", required = false) String plantedTakeTimeL, @RequestParam(name = "plantedTakeTimeG", required = false) String plantedTakeTimeG,
                             @RequestParam(name = "ordTimeL", required = false) String ordTimeL, @RequestParam(name = "ordTimeG", required = false) String ordTimeG) {
        Map<String, Object> params = new HashMap<String, Object>();
        params.put("ordCode", ordCode);
        params.put("status", 1);
        params.put("ordState", ordState);
        params.put("ordTimeRangeId", ordTimeRangeId);
        params.put("plantedTakeTimeL", plantedTakeTimeL);
        params.put("plantedTakeTimeG", plantedTakeTimeG);
        params.put("ordTimeL", ordTimeL);
        params.put("ordTimeG", ordTimeG);
        params.put("ordIds", ids);
        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
        PageInfo<Map<String, Object>> page = new PageInfo<Map<String, Object>>(this.bizOrderService.queryOrdPrintList(tablepar, params));
        //	PageInfo<Map<String,Object>> pageInfo = new PageInfo<Map<String,Object>>(ordList)
        TableSplitResult<Map<String, Object>> result = new TableSplitResult<Map<String, Object>>(page.getPageNum(), page.getTotal(), page.getList());

        return result;

    }


    @GetMapping("ordqrIdx")
    //@RequiresPermissions("ordqrIdx")
    public String ordqrIdx(ModelMap model) {
        String str = "预定订单取餐单打印";
        setTitle(model, new TitleVo("列表", str + "管理", true, "欢迎进入" + str + "页面", true, false));
        return prefix + "/ordqrIdx";
    }

    //@Log(title = "预定打印处理", action = "ordPrt")
    @PostMapping(value = "ordPrt")
    //@RequiresPermissions("dinning:bizOrder:ordPrt")
    @ResponseBody
    public AjaxResult ordPrt(@RequestParam(value = "ordIds", required = false) List<String> ordIds) {
        Map<String, Object> params = new HashMap<String, Object>();
        if (ordIds == null) {
            return AjaxResult.error();
        }
        if (ordIds.size() > 0) {
            params.put("ordIds", ordIds);
        }
        List<Map<String, Object>> ords = (this.bizOrderService.queryOrdPrintList(new Tablepar(), params));

        System.out.println(JSONUtil.toJsonStr(ords));

        List<Map<String, Object>> ordDtls = this.bizOrderService.queryOrdDetailList(params);
        System.out.println(JSONUtil.toJsonStr(ordDtls));
        LinkedHashMap<String, Map<String, Object>> ordMap = new LinkedHashMap<String, Map<String, Object>>();
        for (Map<String, Object> ord : ords) {
            ord.put("dtl", new ArrayList<Map<String, Object>>());
            ordMap.put(ord.get("ordId").toString(), ord);

        }
        for (Map<String, Object> dtl : ordDtls) {
            if (ordMap.containsKey(dtl.get("ordId").toString())) {
                ((List) (ordMap.get(dtl.get("ordId").toString()).get("dtl"))).add(dtl);
            }

        }
        //TableSplitResult<Map<String,Object>> result=new TableSplitResult<Map<String,Object>>(page.getPageNum(), page.getTotal(), page.getList());

        //return  prefix + "/ordPrt";

        System.out.println(JSONUtil.toJsonStr(ordMap));
        if (ordMap.size() > 0) {
            return AjaxResult.successData(200, ordMap);
        }
        return AjaxResult.error();


    }

    @GetMapping("ordFoodSumPrint")
    //@RequiresPermissions("ordqrIdx")
    public String ordFoodSumPrint(ModelMap model) {
        String str = "预定菜品打印";
        setTitle(model, new TitleVo("列表", str + "管理", true, "欢迎进入" + str + "页面", true, false));
        return prefix + "/ordFoodSumPrint";
    }

    @PostMapping(value = "ordDetail")
    @RequiresPermissions("dinning:bizOrder:ordPrtList")
    @ResponseBody
    public AjaxResult ordDetail(@RequestParam(value = "ordId", required = true) String ordId) {
        if (ordId == null || ordId.equals("")) {
            return error(402, "ID错误");
        }
        return retobject(200, bizOrderService.getDetail(ordId));

    }


    /**
     * 新增
     */

    @GetMapping("/add")
    public String add(ModelMap modelMap) {
        return prefix + "/add";
    }

    //@Log(title = "预定订单新增", action = "111")
    @PostMapping(value = "add", produces = "application/json;charset=utf-8")
//	@RequiresPermissions("dinning:bizOrder:add")
    @ResponseBody
    @ApiTokenValid
    public AjaxResult add(@RequestBody @Valid BizOrder bizOrder, BindingResult result) {
        if (result.hasErrors()) {
            List<ObjectError> list = result.getAllErrors();
            for (ObjectError error : list) {
                return retobject(402, error.getDefaultMessage());
            }
        }
        if (bizOrder.getSellTypeName().equals("外卖") && bizOrder.getOrdTimeRangeId() == null) {
            return error(402, "餐段不能为空");
        }
        Boolean b = bizOrderService.saveOrder(bizOrder);
        if (b) {
            return success();
        } else {
            return error();
        }
    }

    /**
     * 删除用户
     *
     * @param ids
     * @return
     */
    //@Log(title = "预定订单删除", action = "111")
    @PostMapping("remove")
    @RequiresPermissions("dinning:bizOrder:remove")
    @ResponseBody
    public AjaxResult remove(String ids) {
        int b = bizOrderService.deleteByPrimaryKey(ids);
        if (b > 0) {
            return success();
        } else {
            return error();
        }
    }

    /**
     * 检查用户
     *
     */
    @PostMapping("checkNameUnique")
    @ResponseBody
    public int checkNameUnique(BizOrder bizOrder) {
        int b = bizOrderService.checkNameUnique(bizOrder);
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
        mmap.put("BizOrder", bizOrderService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }

    /**
     * 修改保存
     */
    //@Log(title = "预定订单修改", action = "111")
//    @RequiresPermissions("dinning:bizOrder:edit")
    @PostMapping(value = "/edit", produces = "application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public AjaxResult editSave(@RequestBody BizOrder record) {
        String res = bizOrderService.updateByPrimaryKeyOrCode(record);
        if (res.equals("")) {
            return success();
        }
        return error(res);
    }

}
