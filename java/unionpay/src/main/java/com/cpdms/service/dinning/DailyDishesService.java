package com.cpdms.service.dinning;

import java.util.ArrayList;
import java.util.Date;
import java.util.List;

import com.cpdms.mapper.dinning.BaseSellTypeMapper;
import com.cpdms.mapper.dinning.BaseTimeRangeMapper;
import com.cpdms.model.dinning.*;
import com.cpdms.shiro.util.ShiroUtils;
import com.cpdms.util.StringUtils;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;
import com.github.pagehelper.PageInfo;
import com.cpdms.common.base.BaseService;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.dinning.DailyDishesMapper;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.util.SnowflakeIdWorker;
import org.springframework.transaction.annotation.Transactional;

/**
 * 每日菜品 DailyDishesService
 *
 * @author Eric_自动生成
 * @Title: DailyDishesService.java 
 * @Package com.cpdms.dinning.service 
 * @email eric@gmail.com
 * @date 2019-10-30 16:15:11  
 **/
@Service
@Transactional
public class DailyDishesService implements BaseService<DailyDishes, DailyDishesExample> {
    @Autowired
    private DailyDishesMapper dailyDishesMapper;
    @Autowired
    private BaseSellTypeMapper baseSellTypeMapper;
    @Autowired
    private BaseTimeRangeMapper baseTimeRangeMapper;


    /**
     * 分页查询
     *
     * @param pageNum
     * @param pageSize
     * @return
     */
    public PageInfo<DailyDishes> list(Tablepar tablepar, String name) {
        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
        List<DailyDishes> list = dailyDishesMapper.selectRelative(name);
        PageInfo<DailyDishes> pageInfo = new PageInfo<DailyDishes>(list);
        return pageInfo;
    }

    /**
     * 分页查询
     *
     * @param pageNum
     * @param pageSize
     * @return
     */
    public PageInfo<DailyDishes> dailyList(Tablepar tablepar, String groupId) {
        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());

        List<DailyDishes> list = dailyDishesMapper.dailyList(groupId);
        PageInfo<DailyDishes> pageInfo = new PageInfo<DailyDishes>(list);
        return pageInfo;
    }

    public DailyDishes dailyGet(String id) {

        return dailyDishesMapper.dailyGet(id);

    }

    @Override
    public int deleteByPrimaryKey(String ids) {

        List<String> lista = Convert.toListStrArray(ids);
        DailyDishesExample example = new DailyDishesExample();
        example.createCriteria().andIdIn(lista);
        DailyDishes dailyDishes = new DailyDishes();
        dailyDishes.setStatus(0);
        return dailyDishesMapper.updateByExampleSelective(dailyDishes, example);


    }

    // 批量保存每日菜品
    public String saveDishes(List<DailyDishes> dailyDishesList) {
        for (DailyDishes dailyDishes : dailyDishesList) {
            // 获取供应方式
            List<BaseSellType> baseSellTypeList = baseSellTypeMapper.selectByFoodId(dailyDishes.getFoodId());
            if (baseSellTypeList.size() == 0) {
                return dailyDishes.getFoodName() + "无供应方式";
            }
            // 获取时段
            List<BaseTimeRange> baseTimeRangeList = baseTimeRangeMapper.selectByFoodId(dailyDishes.getFoodId());
            if (baseTimeRangeList.size() == 0) {
                return dailyDishes.getFoodName() + "无供应餐段";
            }
            // 查询是否已存在
            DailyDishes exist = dailyDishesMapper.getByGroupAndFood(dailyDishes.getGroupId(), dailyDishes.getFoodId());
            if (exist != null) {
                return dailyDishes.getFoodName() + "已存在";

            }
            ArrayList<String> rangNames = new ArrayList<String>();
            ArrayList<String> rangeIds = new ArrayList<String>();
            ArrayList<String> sellTypeNames = new ArrayList<String>();
            ArrayList<String> sellTypeIds = new ArrayList<String>();
            for (BaseSellType baseSellType : baseSellTypeList) {
                sellTypeNames.add(baseSellType.getSellTypeName());
                sellTypeIds.add(baseSellType.getId());
            }
            for (BaseTimeRange baseTimeRange : baseTimeRangeList) {
                rangNames.add(baseTimeRange.getRangeName());
                rangeIds.add(baseTimeRange.getId());
            }
            String createBy = ShiroUtils.getLoginName();

            dailyDishes.setId(SnowflakeIdWorker.getUUID());
            dailyDishes.setCreateBy(createBy);
            dailyDishes.setCreateDate(new Date());
            dailyDishes.setUpdateBy(createBy);
            dailyDishes.setUpdateDate(new Date());

            dailyDishes.setRangeIds(StringUtils.join(rangeIds, "/"));
            dailyDishes.setRangeName(StringUtils.join(rangNames, "/"));
            dailyDishes.setSellTypeIds(StringUtils.join(sellTypeIds, "/"));
            dailyDishes.setSellTypeName(StringUtils.join(sellTypeNames, "/"));
            dailyDishesMapper.insertSelective(dailyDishes);
        }
        return "";
    }

    @Override
    public DailyDishes selectByPrimaryKey(String id) {

        return dailyDishesMapper.selectByPrimaryKey(id);

    }


    @Override
    public int updateByPrimaryKeySelective(DailyDishes record) {
        return dailyDishesMapper.updateByPrimaryKeySelective(record);
    }


    /**
     * 添加
     */
    @Override
    public int insertSelective(DailyDishes record) {
        //添加雪花主键id
        record.setId(SnowflakeIdWorker.getUUID());


        return dailyDishesMapper.insertSelective(record);
    }


    @Override
    public int updateByExampleSelective(DailyDishes record, DailyDishesExample example) {

        return dailyDishesMapper.updateByExampleSelective(record, example);
    }


    @Override
    public int updateByExample(DailyDishes record, DailyDishesExample example) {

        return dailyDishesMapper.updateByExample(record, example);
    }

    @Override
    public List<DailyDishes> selectByExample(DailyDishesExample example) {

        return dailyDishesMapper.selectByExample(example);
    }


    @Override
    public long countByExample(DailyDishesExample example) {

        return dailyDishesMapper.countByExample(example);
    }


    @Override
    public int deleteByExample(DailyDishesExample example) {

        return dailyDishesMapper.deleteByExample(example);
    }

    public List<DailyDishes> getIdsByGroupId(String groupId) {
        return dailyDishesMapper.getIdsByGroupId(groupId);
    }

    /**
     * 检查name
     *
     * @param dailyDishes
     * @return
     */
    public int checkNameUnique(DailyDishes dailyDishes) {
        DailyDishesExample example = new DailyDishesExample();
        example.createCriteria().andGroupIdEqualTo(dailyDishes.getGroupId());
        List<DailyDishes> list = dailyDishesMapper.selectByExample(example);
        return list.size();
    }


}
