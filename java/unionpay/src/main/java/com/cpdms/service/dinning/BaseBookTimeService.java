package com.cpdms.service.dinning;

import java.util.List;
import java.util.Arrays;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;
import com.github.pagehelper.PageInfo;
import com.cpdms.common.base.BaseService;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.dinning.BaseBookTimeMapper;
import com.cpdms.model.dinning.BaseBookTime;
import com.cpdms.model.dinning.BaseBookTimeExample;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.util.SnowflakeIdWorker;

/**
 * 预定时间 BaseBookTimeService
 * @Title: BaseBookTimeService.java 
 * @Package com.cpdms.dinning.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:48:30  
 **/
@Service
public class BaseBookTimeService implements BaseService<BaseBookTime, BaseBookTimeExample>{
	@Autowired
	private BaseBookTimeMapper baseBookTimeMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<BaseBookTime> list(Tablepar tablepar,String name){
	        BaseBookTimeExample testExample=new BaseBookTimeExample();
	        testExample.setOrderByClause("create_date DESC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andFoodIdLike("%"+name+"%");
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BaseBookTime> list= baseBookTimeMapper.selectByExample(testExample);
	        PageInfo<BaseBookTime> pageInfo = new PageInfo<BaseBookTime>(list);
	        return  pageInfo;
	 }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista=Convert.toListStrArray(ids);
			BaseBookTimeExample example=new BaseBookTimeExample();
			example.createCriteria().andIdIn(lista);
			return baseBookTimeMapper.deleteByExample(example);


	}


	@Override
	public BaseBookTime selectByPrimaryKey(String id) {

			return baseBookTimeMapper.selectByPrimaryKey(id);

	}


	@Override
	public int updateByPrimaryKeySelective(BaseBookTime record) {
		return baseBookTimeMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(BaseBookTime record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return baseBookTimeMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(BaseBookTime record, BaseBookTimeExample example) {

		return baseBookTimeMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(BaseBookTime record, BaseBookTimeExample example) {

		return baseBookTimeMapper.updateByExample(record, example);
	}

	@Override
	public List<BaseBookTime> selectByExample(BaseBookTimeExample example) {

		return baseBookTimeMapper.selectByExample(example);
	}


	@Override
	public long countByExample(BaseBookTimeExample example) {

		return baseBookTimeMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(BaseBookTimeExample example) {

		return baseBookTimeMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param baseBookTime
	 * @return
	 */
	public int checkNameUnique(BaseBookTime baseBookTime){
		BaseBookTimeExample example=new BaseBookTimeExample();
		example.createCriteria().andFoodIdEqualTo(baseBookTime.getFoodId());
		List<BaseBookTime> list=baseBookTimeMapper.selectByExample(example);
		return list.size();
	}


}
