package com.cpdms.service.hair;

import java.util.Date;
import java.util.List;

import cn.hutool.core.date.DateUtil;
import com.cpdms.common.base.BaseService;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.hair.BaseHairSettingMapper;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.hair.BaseHairSetting;
import com.cpdms.model.hair.BaseHairSettingExample;
import com.cpdms.util.DateUtils;
import com.cpdms.util.SnowflakeIdWorker;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;

/**
 *  BaseHairSettingService
 * @Title: BaseHairSettingService.java 
 * @Package com.cpdms.vote.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-12-06 13:00:59  
 **/
@Service
public class BaseHairSettingService implements BaseService<BaseHairSetting, BaseHairSettingExample> {
	@Autowired
	private BaseHairSettingMapper baseHairSettingMapper;

	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<BaseHairSetting> list(Tablepar tablepar){
	        BaseHairSettingExample testExample=new BaseHairSettingExample();
	        testExample.setOrderByClause("create_date DESC");
	        // 获取最新一条生效记录
            BaseHairSetting stepTimeOld = getSetting("STEP_TIME");
            BaseHairSetting stepNumOld = getSetting("NUM_PER_STEP");
	        testExample.createCriteria().andStartTimeGreaterThanOrEqualTo(stepTimeOld.getStartTime()).andSettingKeyEqualTo("STEP_TIME");
	        testExample.or().andStartTimeGreaterThanOrEqualTo(stepNumOld.getStartTime()).andSettingKeyEqualTo("NUM_PER_STEP");

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BaseHairSetting> list= baseHairSettingMapper.selectByExample(testExample);
	        PageInfo<BaseHairSetting> pageInfo = new PageInfo<BaseHairSetting>(list);
	        return  pageInfo;
	 }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista= Convert.toListStrArray(ids);
			BaseHairSettingExample example=new BaseHairSettingExample();
			example.createCriteria().andIdIn(lista);
			return baseHairSettingMapper.deleteByExample(example);


	}

	@Override
	public BaseHairSetting selectByPrimaryKey(String id) {

			return baseHairSettingMapper.selectByPrimaryKey(id);

	}

	public BaseHairSetting getSetting(String settingKey) {
	     return baseHairSettingMapper.selectSetting(settingKey, DateUtils.format(new Date(), DateUtils.DATE_PATTERN));
    }


	@Override
	public int updateByPrimaryKeySelective(BaseHairSetting record) {
	     // 先计算生效时间，然后查当天有没有记录，有就更新没有就插入
        BaseHairSettingExample testExample=new BaseHairSettingExample();
        String nextWeek = DateUtils.format(DateUtil.nextWeek(), DateUtils.YYYY_MM_DD);
        testExample.createCriteria().andSettingKeyEqualTo(record.getSettingKey()).andStartTimeEqualTo(nextWeek);
        List<BaseHairSetting> baseHairSettings = baseHairSettingMapper.selectByExample(testExample);
        if (baseHairSettings.size() > 0) {
            BaseHairSetting baseHairSetting = baseHairSettings.get(0);
            baseHairSetting.setSettingValue(record.getSettingValue());
            return baseHairSettingMapper.updateByPrimaryKeySelective(baseHairSetting);
        } else {
            record.setId(SnowflakeIdWorker.getUUID());
            record.setStartTime(nextWeek);
            record.setCreateDate(new Date());
            return baseHairSettingMapper.insertSelective(record);
        }
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(BaseHairSetting record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return baseHairSettingMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(BaseHairSetting record, BaseHairSettingExample example) {

		return baseHairSettingMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(BaseHairSetting record, BaseHairSettingExample example) {

		return baseHairSettingMapper.updateByExample(record, example);
	}

	@Override
	public List<BaseHairSetting> selectByExample(BaseHairSettingExample example) {

		return baseHairSettingMapper.selectByExample(example);
	}


	@Override
	public long countByExample(BaseHairSettingExample example) {

		return baseHairSettingMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(BaseHairSettingExample example) {

		return baseHairSettingMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param baseHairSetting
	 * @return
	 */
	public int checkNameUnique(BaseHairSetting baseHairSetting){
		BaseHairSettingExample example=new BaseHairSettingExample();
		example.createCriteria().andSettingKeyEqualTo(baseHairSetting.getSettingKey());
		List<BaseHairSetting> list=baseHairSettingMapper.selectByExample(example);
		return list.size();
	}


}
