package com.cpdms.service.dinning;

import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.Arrays;

import com.cpdms.mapper.dinning.*;
import com.cpdms.model.dinning.*;
import com.cpdms.shiro.util.ShiroUtils;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import com.github.pagehelper.PageHelper;
import com.github.pagehelper.PageInfo;
import com.cpdms.common.base.BaseService;
import com.cpdms.common.support.Convert;
import com.cpdms.model.auto.TSysRoleUser;
import com.cpdms.model.auto.TsysUser;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.util.SnowflakeIdWorker;
import com.cpdms.util.StringUtils;

/**
 * 菜品设置 BaseFoodService
 * @Title: BaseFoodService.java 
 * @Package com.cpdms.dinning.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:50:37  
 **/
@Service
public class BaseFoodService implements BaseService<BaseFood, BaseFoodExample>{
	@Autowired
	private BaseFoodMapper baseFoodMapper;
	@Autowired
	private RefFoodTimeMapper refFoodTimeMapper;
	@Autowired
	private RefFoodSellMapper refFoodSellMapper;
	@Autowired
    private DailyDishesMapper dailyDishesMapper;
	@Autowired
    private BaseSellTypeMapper baseSellTypeMapper;
	@Autowired
    private BaseTimeRangeMapper baseTimeRangeMapper;
	@Autowired
    private ImgUrlMapper imgUrlMapper;

	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<BaseFood> list(Tablepar tablepar,BaseFood baseFood){
	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BaseFood> list= baseFoodMapper.selectRelative(baseFood);
	        PageInfo<BaseFood> pageInfo = new PageInfo<BaseFood>(list);
	        return  pageInfo;
	 }

	  /**
	 * 获取所有外卖
	 * @return
	 */
	 public PageInfo<BaseFood> getAll(Tablepar tablepar, ArrayList<String> array){
	     PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BaseFood> list= baseFoodMapper.selectFoodsAndImgs(array);
	        PageInfo<BaseFood> pageInfo = new PageInfo<BaseFood>(list);
	        return pageInfo;
	 }

	 /**
	 * 获取所有外卖
	 * @return
	 */
	 public BaseFood getOne(String id){
	     List<String> ids = new ArrayList<>();
	     ids.add(id);
        List<BaseFood> list= baseFoodMapper.selectFoodsAndImgs(ids);
        if (list.size() > 0) {
            return list.get(0);
        }

        return null;
	 }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista=Convert.toListStrArray(ids);
			BaseFoodExample example=new BaseFoodExample();
			example.createCriteria().andIdIn(lista);
			BaseFood baseFood = new BaseFood();
			baseFood.setStatus(0);
			baseFood.setUpdateDate(new Date());
			// 删除图片
			List<BaseFood> baseFoods = baseFoodMapper.selectByExample(example);
			List<String> imgIds = new ArrayList<String>();
			if (baseFoods.size() == 0) {
			    return 0;
            }
            for (BaseFood b : baseFoods) {
                imgIds.add(b.getImgId());
            }
            ImgUrlExample imgUrlExample = new ImgUrlExample();
            imgUrlExample.createCriteria().andImgIdIn(imgIds);
            imgUrlMapper.deleteByExample(imgUrlExample);
            // 删除每日菜品
            DailyDishesExample dailyDishesExample = new DailyDishesExample();
            dailyDishesExample.createCriteria().andFoodIdIn(lista).andFoodEndTimeGreaterThan(new Date());
            DailyDishes dailyDishes = new DailyDishes();
            dailyDishes.setStatus(0);
            dailyDishes.setUpdateDate(new Date());
            dailyDishesMapper.updateByExampleSelective(dailyDishes, dailyDishesExample);
            // 删除供应方式关联表
            RefFoodSellExample refFoodSellExample = new RefFoodSellExample();
            refFoodSellExample.createCriteria().andFoodIdIn(lista);
            refFoodSellMapper.deleteByExample(refFoodSellExample);
            // 删除餐段关联表
            RefFoodTimeExample refFoodTimeExample = new RefFoodTimeExample();
            refFoodTimeExample.createCriteria().andFoodIdIn(lista);
            refFoodTimeMapper.deleteByExample(refFoodTimeExample);

			return baseFoodMapper.updateByExampleSelective(baseFood, example);


	}


	@Override
	public BaseFood selectByPrimaryKey(String id) {

			return baseFoodMapper.selectByPrimaryKey(id);

	}

	public BaseFood selectOne(String id) {

			return baseFoodMapper.selectOne(id);

	}


	@Override
	public int updateByPrimaryKeySelective(BaseFood record) {
		return baseFoodMapper.updateByPrimaryKeySelective(record);
	}

	/**
	 * 添加用户
	 */
	@Override
	public int insertSelective(BaseFood record) {
		return baseFoodMapper.insertSelective(record);
	}

	/**
	 * 添加
	 */
	@Transactional
	public int insertSelective(BaseFood record, List<String> rangeIds, List<String> sellTypeIds) {
					//添加雪花主键id
			String foodId = SnowflakeIdWorker.getUUID();
			record.setId(foodId);
			int b = baseFoodMapper.insertSelective(record);
			this.insertMiddleTable(record, rangeIds, sellTypeIds);
		return b;

	}


	@Override
	public int updateByExampleSelective(BaseFood record, BaseFoodExample example) {

		return baseFoodMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(BaseFood record, BaseFoodExample example) {

		return baseFoodMapper.updateByExample(record, example);
	}

	@Override
	public List<BaseFood> selectByExample(BaseFoodExample example) {

		return baseFoodMapper.selectByExample(example);
	}


	@Override
	public long countByExample(BaseFoodExample example) {

		return baseFoodMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(BaseFoodExample example) {

		return baseFoodMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param baseFood
	 * @return
	 */
	public int checkNameUnique(BaseFood baseFood){
		BaseFoodExample example=new BaseFoodExample();
		example.createCriteria().andFoodCodeEqualTo(baseFood.getFoodCode());
		List<BaseFood> list=baseFoodMapper.selectByExample(example);
		return list.size();
	}

	/**
	 * 关联表添加
	 */
	public void insertMiddleTable(BaseFood record, List<String> rangeIds, List<String> sellTypeIds) {
		if (StringUtils.isNotEmpty(rangeIds)) {
			for (String rangeId : rangeIds) {
				RefFoodTime foodTime=new RefFoodTime(SnowflakeIdWorker.getUUID(), rangeId,record.getId());
				refFoodTimeMapper.insertSelective(foodTime);
			}
		}
		if (StringUtils.isNotEmpty(sellTypeIds)) {
			for (String sellTypeId : sellTypeIds) {
				RefFoodSell foodSell=new RefFoodSell(SnowflakeIdWorker.getUUID(), sellTypeId,record.getId());
				refFoodSellMapper.insertSelective(foodSell);
			}
		}
	}
	/**
	 * 关联表删除
	 */
	public void deleteMiddleTable(BaseFood record, List<String> rangeIds, List<String> sellTypeIds) {
		if (StringUtils.isNotEmpty(rangeIds)) {
			for (String rangeId : rangeIds) {
				refFoodTimeMapper.deleteByTimeIdAndFoodId(rangeId, record.getId());
			}
		}
		if (StringUtils.isNotEmpty(sellTypeIds)) {
			for (String sellTypeId : sellTypeIds) {
				refFoodSellMapper.deleteBySellIdAndFoodId(sellTypeId, record.getId());
			}
		}
	}

	/*
	* 修改每日菜品
	* */
	public void modifyDailyDishes(BaseFood record, List<String> rangeIds, List<String> sellTypeIds) {
	    DailyDishesExample dailyDishesExample = new DailyDishesExample();
	    dailyDishesExample.createCriteria().andFoodIdEqualTo(record.getId()).andFoodEndTimeGreaterThan(new Date());
	    List<DailyDishes> dailyDishesList =  dailyDishesMapper.selectByExample(dailyDishesExample);
	    if (dailyDishesList.size() == 0) {
	        return;
        }
        BaseSellTypeExample baseSellTypeExample = new BaseSellTypeExample();
	    baseSellTypeExample.createCriteria().andIdIn(sellTypeIds);
	    List<BaseSellType> baseSellTypeList = baseSellTypeMapper.selectByExample(baseSellTypeExample);

	    BaseTimeRangeExample baseTimeRangeExample = new BaseTimeRangeExample();
	    baseTimeRangeExample.createCriteria().andIdIn(rangeIds);
	    List<BaseTimeRange> baseTimeRangeList = baseTimeRangeMapper.selectByExample(baseTimeRangeExample);

	    String rangName = "";
	    String rangeId = "";
	    String sellName = "";
	    String sellId = "";

	    if (baseSellTypeList.size() > 0) {
	        ArrayList<String> sellTypeNameList = new ArrayList<String>();
            ArrayList<String> sellTypeIdList = new ArrayList<String>();
            for (BaseSellType baseSellType : baseSellTypeList) {
                sellTypeNameList.add(baseSellType.getSellTypeName());
                sellTypeIdList.add(baseSellType.getId());
            }
            sellName = StringUtils.join(sellTypeNameList, "/");
            sellId = StringUtils.join(sellTypeIdList, "/");
        }
        if (baseTimeRangeList.size() > 0) {
            ArrayList<String> rangeNameList = new ArrayList<String>();
            ArrayList<String> rangIdList = new ArrayList<String>();
            for (BaseTimeRange baseTimeRange : baseTimeRangeList) {
                rangeNameList.add(baseTimeRange.getRangeName());
                rangIdList.add(baseTimeRange.getId());
            }
            rangName = StringUtils.join(rangeNameList, "/");
            rangeId = StringUtils.join(rangIdList, "/");
        }

        for (DailyDishes dailyDishes : dailyDishesList) {
            dailyDishes.setSellTypeName(sellName);
            dailyDishes.setSellTypeIds(sellId);
            dailyDishes.setRangeName(rangName);
            dailyDishes.setRangeIds(rangeId);
            dailyDishes.setUpdateBy(ShiroUtils.getLoginName());
            dailyDishes.setUpdateDate(new Date());
            dailyDishes.setFoodName(record.getFoodName());
            dailyDishes.setFoodPrice(record.getFoodPrice());
            dailyDishes.setFoodStatus(record.getFoodStatus());
            dailyDishesMapper.updateByPrimaryKeySelective(dailyDishes);
        }
    }
}
