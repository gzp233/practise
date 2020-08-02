package com.cpdms.controller.dinning;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.HashMap;
import java.util.List;

import com.cpdms.common.token.ApiTokenValid;
import com.cpdms.model.dinning.*;
import com.cpdms.service.dinning.*;
import org.apache.shiro.authz.annotation.RequiresPermissions;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.ui.ModelMap;
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
import com.cpdms.util.DateUtils;
import com.cpdms.util.SnowflakeIdWorker;
import com.github.pagehelper.PageInfo;

@Controller
@RequestMapping("BaseFoodController")

public class BaseFoodController extends BaseController{

	private String prefix = "dinning/baseFood";

	@Autowired
	private BaseFoodService baseFoodService;
	@Autowired
	private ImgUrlService imgUrlService;
	@Autowired
	private BaseTimeRangeService rangeService;
	@Autowired
	private BaseSellTypeService sellTypeService;
	@Autowired
	private RefFoodTimeService refFoodTimeService;
	@Autowired
	private RefFoodSellService refFoodSellService;
	@Autowired
    private BaseSellTypeService baseSellTypeService;
	@Autowired
	private FoodNumberService foodNumberService;
	@Autowired
	private DailyDishesService dailyDishesService;

	@GetMapping("view")
	@RequiresPermissions("dinning:baseFood:view")
    public String view(ModelMap model)
    {
		String str="菜品设置";
		setTitle(model, new TitleVo("列表", str+"管理", true,"欢迎进入"+str+"页面", true, false));
        return prefix + "/list";
    }

	//@Log(title = "菜品设置集合查询", action = "111")
	@PostMapping("list")
	@RequiresPermissions("dinning:baseFood:list")
	@ResponseBody
	public Object list(Tablepar tablepar,BaseFood baseFood){

		PageInfo<BaseFood> page=baseFoodService.list(tablepar,baseFood) ;
		TableSplitResult<BaseFood> result=new TableSplitResult<BaseFood>(page.getPageNum(), page.getTotal(), page.getList());
		return  result;
	}

	//@Log(title = "根据时间段获取所有菜品", action = "111")
	@PostMapping(value = "listJson",produces="application/json;charset=utf-8")
//	@RequiresPermissions("dinning:baseFood:getAll")
	@ResponseBody
	@ApiTokenValid
	public Object listJson(@RequestBody HashMap<String, Object> params){
	    Integer pageNum = (Integer)params.get("pageNum");
		Integer pageSize = (Integer)params.get("pageSize");
		Tablepar tablepar = new Tablepar();
		tablepar.setPageNum(pageNum);
		tablepar.setPageSize(pageSize);
		String dateStr = params.get("date").toString();
		Date paramDate = DateUtils.dateTime(DateUtils.YYYY_MM_DD ,dateStr);

	    String timeId = "";
	    String sellTypeName = "";
	    if (params.get("timeId") != null && !params.get("timeId").toString().equals("")) {
            timeId = params.get("timeId").toString();
        }
        if (params.get("sellTypeName") != null && !params.get("sellTypeName").toString().equals("")) {
            sellTypeName = params.get("sellTypeName").toString();
        }
        if (sellTypeName.equals("")) {
            return error(402, "售卖方式不能为空");
        }
        if (timeId.equals("")) {
            return error(402, "餐段不能为空");
        }
		FoodNumber foodNumber = foodNumberService.getByDate(paramDate);
        if (foodNumber == null) {
			return error(402, "暂无菜品期数");
		}
        // 获取售卖类型的菜品ID
        List<BaseSellType> baseSellTypes = baseSellTypeService.getByName(sellTypeName);
	    if (baseSellTypes.size() != 1) {
                return error(402, "该售卖类型未设置");
        }
        ArrayList<String> sellArray = new ArrayList<String>();
        List<RefFoodSell> refFoodSells = refFoodSellService.getFoods(baseSellTypes.get(0).getId());
        if (refFoodSells.size() == 0) {
            return retobject(200, new ArrayList<>());
        }
        for (RefFoodSell obj : refFoodSells) {
            sellArray.add(obj.getFoodId());
        }

        // 获取时间段
        ArrayList<String> timeIds = new ArrayList<String>();
        Date date = new Date();
        SimpleDateFormat sdf = new SimpleDateFormat("HH:mm");

        List<BaseTimeRange> baseTimeRangeList = new ArrayList<>();
        if (DateUtils.getStartTime(paramDate).before(new Date())) {// 如果是当天的
            baseTimeRangeList = rangeService.getByTime(sdf.format(date));
        } else {// 获取所有
            baseTimeRangeList = rangeService.getAll();
        }

        if (baseTimeRangeList.size() == 0) {
            return retobject(200, new ArrayList<>());
        }
        for (BaseTimeRange baseTimeRange:baseTimeRangeList) {
            if (baseTimeRange.getId().equals(timeId)) {
                timeIds.add(timeId);
            }
        }
        if (timeIds.size() == 0) {
           return retobject(200, new ArrayList<>());
        }

        // 获取时间段的菜品ID
        ArrayList<String> timeArray = new ArrayList<>();
        List<RefFoodTime> refFoodTimes = refFoodTimeService.getFoods(timeIds);
        if (refFoodTimes.size() == 0) {
            return retobject(200, new ArrayList<>());
        }
        for (RefFoodTime obj : refFoodTimes) {
            timeArray.add(obj.getFoodId());
        }
        sellArray.retainAll(timeArray);
        // 获取期数ID
		ArrayList<String> foodNumberArray = new ArrayList<>();
		List<DailyDishes> dailyDishes = dailyDishesService.getIdsByGroupId(foodNumber.getId());
		if (dailyDishes.size() == 0) {
			return retobject(200, new ArrayList<>());
		}
		for (DailyDishes obj : dailyDishes) {
			foodNumberArray.add(obj.getFoodId());
		}
		sellArray.retainAll(foodNumberArray);

        if (sellArray.size() == 0) {
            return retobject(200, new ArrayList<>());
        }

        PageInfo<BaseFood> page= baseFoodService.getAll(tablepar, sellArray);
        return retobject(200, page);

	}

	@PostMapping(value = "getOne",produces="application/json;charset=utf-8")
    @ResponseBody
	@ApiTokenValid
	public Object getOne(@RequestBody HashMap<String, Object> params) {
	    String id = params.get("id").toString();

	    return retobject(200, baseFoodService.getOne(id));
    }

	/**
     * 新增
     */

    @GetMapping("/add")
    public String add(ModelMap modelMap)
    {
    	//餐段
    	List<BaseTimeRange>timeRangeList = rangeService.queryList();
    	modelMap.put("timeRangeList",timeRangeList);
    	//供应方式
    	List<BaseSellType>sellTypeList = sellTypeService.queryList();
    	modelMap.put("sellTypeList",sellTypeList);
        return prefix + "/add";
    }

	//@Log(title = "菜品设置新增", action = "111")
	@PostMapping("add")
	@RequiresPermissions("dinning:baseFood:add")
	@ResponseBody
	public AjaxResult add(BaseFood baseFood, Model model,@RequestParam(value="rangeId", required = false)List<String> rangeIds,
			@RequestParam(value="sellTypeId", required = false)List<String> sellTypeIds){
        if (sellTypeIds == null || sellTypeIds.size() == 0) {
            return error("售卖方式至少选择一个");
        }
        if (rangeIds == null || rangeIds.size() == 0) {
            return error("售卖餐段至少选择一个");
        }
        if (baseFood.getFoodPrice() == null) {
			return error("价格不能为空");
		}
        baseFood.setFoodCode(SnowflakeIdWorker.getUUID());
		ImgUrl img = new ImgUrl();
		String oldImgId = baseFood.getImgId();
		//插入菜品
		baseFood.setImgId(SnowflakeIdWorker.getUUID());
		int b=baseFoodService.insertSelective(baseFood, rangeIds, sellTypeIds);
		if(b<=0){
			return error();
		}
		//更新图片
		img.setImgId(baseFood.getImgId());
		img.setId(oldImgId);
		System.out.println(img);
		int a = imgUrlService.updateByPrimaryKeySelective(img);
		if(a <= 0){
			return error();
		}
		//插入关联表

		return success();

	}

	/**
	 * 删除用户
	 * @param ids
	 * @return
	 */
	//@Log(title = "菜品设置删除", action = "111")
	@PostMapping("remove")
	@RequiresPermissions("dinning:baseFood:remove")
	@ResponseBody
	public AjaxResult remove(String ids){
		int b=baseFoodService.deleteByPrimaryKey(ids);
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
	public int checkNameUnique(BaseFood baseFood){
		int b=baseFoodService.checkNameUnique(baseFood);
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
		//查询当前餐品对应的餐段与供应方式
		List<RefFoodTime> timeList = refFoodTimeService.selectByFoodId(id);
		List<RefFoodSell> sellList = refFoodSellService.selectByFoodId(id);
		//餐段
    	List<BaseTimeRange>timeRangeList = rangeService.queryList();
    	for (BaseTimeRange baseTimeRange : timeRangeList) {
    		for (RefFoodTime foodTime : timeList) {
    			if(baseTimeRange.isIscheck()) continue;
    			if (baseTimeRange.getId().equals(foodTime.getTimeId())) {
    				baseTimeRange.setIscheck(true);
    			} else {
    				baseTimeRange.setIscheck(false);
    			}
    		}
    	}
    	//供应方式
    	List<BaseSellType>sellTypeList = sellTypeService.queryList();

    	for (BaseSellType baseSellType : sellTypeList) {
    		for (RefFoodSell foodSell : sellList) {
    			//System.out.println("baseSellType"+baseSellType.getId());
    			System.out.println("foodSell"+foodSell.getSellTypeId());
    			if (baseSellType.isIscheck()) continue;
    			if (baseSellType.getId().equals(foodSell.getSellTypeId())) {
    				baseSellType.setIscheck(true);
    			} else {
    				baseSellType.setIscheck(false);
    			}
    			System.out.println(baseSellType.isIscheck());
    		}
    	}
    	mmap.put("timeRangeList",timeRangeList);
    	mmap.put("sellTypeList",sellTypeList);
        mmap.put("BaseFood", baseFoodService.selectOne(id));

        return prefix + "/edit";
    }

	/**
     * 修改保存
     */
    //@Log(title = "菜品设置修改", action = "111")
    @RequiresPermissions("dinning:baseFood:edit")
    @PostMapping("/edit")
    @ResponseBody
    public AjaxResult editSave(BaseFood record, Model model,@RequestParam(value="rangeId", required = false)List<String> rangeIds,
			@RequestParam(value="sellTypeId", required = false)List<String> sellTypeIds) {
        if (sellTypeIds == null || sellTypeIds.size() == 0) {
            return error("售卖方式至少选择一个");
        }
        if (rangeIds == null || rangeIds.size() == 0) {
            return error("售卖餐段至少选择一个");
        }
		if (record.getFoodPrice() == null) {
			return error("价格不能为空");
		}
        record.setFoodCode(SnowflakeIdWorker.getUUID());
    	//删除关联关系
    	baseFoodService.deleteMiddleTable(record, rangeIds, sellTypeIds);
    	//重新插入关联关系
    	baseFoodService.insertMiddleTable(record, rangeIds, sellTypeIds);
    	//修改每日菜品表
        baseFoodService.modifyDailyDishes(record, rangeIds, sellTypeIds);
        ImgUrl img = new ImgUrl();
		String oldImgId = record.getImgId();
		//插入菜品
		record.setImgId(SnowflakeIdWorker.getUUID());
		int b=baseFoodService.updateByPrimaryKeySelective(record);
		if(b<=0){
			return error();
		}
		//更新图片
		img.setImgId(record.getImgId());
		img.setId(oldImgId);
		System.out.println(img);
		int a = imgUrlService.updateByPrimaryKeySelective(img);
		if(a <= 0){
			return error();
		}

		return success();
    }





}
