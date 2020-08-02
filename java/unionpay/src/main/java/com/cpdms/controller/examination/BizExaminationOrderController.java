package com.cpdms.controller.examination;

import java.util.Date;
import java.util.HashMap;
import java.util.List;

import javax.validation.Valid;

import com.cpdms.common.token.ApiTokenValid;
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
import com.cpdms.model.examination.BizExaminationOrder;
import com.cpdms.model.examination.BizExaminationOrderExample;
import com.cpdms.service.examination.BizExaminationOrderService;
import com.cpdms.util.StringUtils;

@Controller
@RequestMapping("BizExaminationOrderController")

public class BizExaminationOrderController extends BaseController {

    private String prefix = "examination/bizExaminationOrder";
    @Autowired
    private BizExaminationOrderService bizExaminationOrderService;

    @GetMapping("view")
    @RequiresPermissions("examination:bizExaminationOrder:view")
    public String view(ModelMap model) {
        String str = "体检预约表";
        setTitle(model, new TitleVo("列表", str + "管理", true, "欢迎进入" + str + "页面", true, false));
        return prefix + "/list";
    }

    //@Log(title = "体检预约表集合查询", action = "111")
    @PostMapping("list")
    @RequiresPermissions("examination:bizExaminationOrder:list")
    @ResponseBody
    public Object list(Tablepar tablepar, String searchTxt) {
        BizExaminationOrderExample example = new BizExaminationOrderExample();
        example.createCriteria().andStatusEqualTo(1);
        if (StringUtils.isNotEmpty(searchTxt)) {
            example.createCriteria().andOrdCodeLike("%" + searchTxt + "%");
        }

        PageInfo<BizExaminationOrder> page = bizExaminationOrderService.list(tablepar, example);
        TableSplitResult<BizExaminationOrder> result = new TableSplitResult<BizExaminationOrder>(page.getPageNum(), page.getTotal(), page.getList());
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
        BizExaminationOrderExample example = new BizExaminationOrderExample();
        example.createCriteria().andStatusEqualTo(1).andCardNoEqualTo(cardNo);
        PageInfo<BizExaminationOrder> page = bizExaminationOrderService.list(tablepar, example);
        TableSplitResult<BizExaminationOrder> result = new TableSplitResult<BizExaminationOrder>(page.getPageNum(), page.getTotal(), page.getList());
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
        return retobject(200, bizExaminationOrderService.selectByPrimaryKey(id));
    }

    /**
     * 新增
     */

    @GetMapping("/add")
    public String add(ModelMap modelMap) {
        return prefix + "/add";
    }

    //@Log(title = "体检预约表新增", action = "111")
    @PostMapping(value = "add", produces = "application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public AjaxResult add(@RequestBody @Valid BizExaminationOrder bizExaminationOrder, BindingResult result) {
        if (result.hasErrors()) {
            List<ObjectError> list = result.getAllErrors();
            for (ObjectError error : list) {
                return error(402, error.getDefaultMessage());
            }
        }
        if (DateUtils.dateTime(DateUtils.YYYY_MM_DD, bizExaminationOrder.getOrdTime()) == null) {
            return error(402, "预定时间格式错误");
        }

        HashMap<String, String> b = bizExaminationOrderService.saveOrder(bizExaminationOrder);

        if (!b.get("code").equals("0")) {
            return error(402, b.get("msg"));
        }

        return retobject(200, b.get("msg"));
    }

    //  确认订单
    @PostMapping(value = "confirm")
    @ResponseBody
    public AjaxResult confirm(@RequestParam(value = "id", required = true) String id) {
        BizExaminationOrder bizExaminationOrder = new BizExaminationOrder();
        bizExaminationOrder.setOrdState(Constants.EXAMINATION_ORD_FINISHED);
        bizExaminationOrder.setFinishTime(DateUtils.format(new Date(), DateUtils.DATE_PATTERN));
        bizExaminationOrder.setUpdateDate(new Date());
        bizExaminationOrder.setId(id);
        int b = bizExaminationOrderService.updateByPrimaryKeySelective(bizExaminationOrder);
        if (b <= 0) {
            return error(402, "确认失败");
        }
        return retobject(200, "确认成功");

    }

    /**
     * 删除用户
     *
     * @param ids
     * @return
     */
    //@Log(title = "体检预约表删除", action = "111")
    @PostMapping("remove")
    @RequiresPermissions("examination:bizExaminationOrder:remove")
    @ResponseBody
    public AjaxResult remove(String ids) {
        int b = bizExaminationOrderService.deleteByPrimaryKey(ids);
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
    public int checkNameUnique(BizExaminationOrder bizExaminationOrder) {
        int b = bizExaminationOrderService.checkNameUnique(bizExaminationOrder);
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
        mmap.put("BizExaminationOrder", bizExaminationOrderService.selectByPrimaryKey(id));

        return prefix + "/edit";
    }

    /**
     * 修改保存
     */
    //@Log(title = "体检预约表修改", action = "111")
    @PostMapping(value = "/edit", produces = "application/json;charset=utf-8")
    @ResponseBody
    @ApiTokenValid
    public AjaxResult editSave(@RequestBody BizExaminationOrder record) {
        return toAjax(bizExaminationOrderService.updateByPrimaryKeySelective(record));
    }


}
