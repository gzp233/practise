package com.cpdms.service.dinning;

import java.util.List;
import java.util.ArrayList;
import java.util.Arrays;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;
import com.github.pagehelper.PageInfo;
import com.cpdms.common.base.BaseService;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.dinning.BaseFoodMapper;
import com.cpdms.mapper.dinning.BaseTimeRangeMapper;
import com.cpdms.model.dinning.BaseFood;
import com.cpdms.model.dinning.BaseTimeRange;
import com.cpdms.model.dinning.BaseTimeRangeExample;
import com.cpdms.model.auto.TsysRole;
import com.cpdms.model.auto.TsysRoleExample;
import com.cpdms.model.custom.RoleVo;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.util.SnowflakeIdWorker;

/**
 * 餐段设置 BaseTimeRangeService
 * @Title: BaseTimeRangeService.java 
 * @Package com.cpdms.dinning.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:49:18  
 **/
@Service
public class BaseTimeRangeService implements BaseService<BaseTimeRange, BaseTimeRangeExample>{
	@Autowired
	private BaseTimeRangeMapper baseTimeRangeMapper;

	@Autowired
	private BaseFoodMapper baseFoodeMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<BaseTimeRange> list(Tablepar tablepar,String name){
	        BaseTimeRangeExample testExample=new BaseTimeRangeExample();
	        testExample.setOrderByClause("create_date DESC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andRangeNameLike("%"+name+"%");
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BaseTimeRange> list= baseTimeRangeMapper.selectByExample(testExample);
	        PageInfo<BaseTimeRange> pageInfo = new PageInfo<BaseTimeRange>(list);
	        return  pageInfo;
	 }

	 /**
	 * 获取所有
	 * @return
	 */
	 public List<BaseTimeRange> getAll(){
	        BaseTimeRangeExample testExample=new BaseTimeRangeExample();
            testExample.createCriteria().andStatusEqualTo(1);
	        List<BaseTimeRange> list= baseTimeRangeMapper.selectByExample(testExample);
	        return list;
	 }

	 public List<BaseTimeRange> getByTime(String endTime){
	        BaseTimeRangeExample testExample=new BaseTimeRangeExample();
            testExample.createCriteria().andEndDateGreaterThan(endTime);
	        List<BaseTimeRange> list= baseTimeRangeMapper.selectByExample(testExample);
	        return list;
	 }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista=Convert.toListStrArray(ids);
			BaseTimeRangeExample example=new BaseTimeRangeExample();
			example.createCriteria().andIdIn(lista);
			return baseTimeRangeMapper.deleteByExample(example);


	}


	@Override
	public BaseTimeRange selectByPrimaryKey(String id) {

			return baseTimeRangeMapper.selectByPrimaryKey(id);

	}


	@Override
	public int updateByPrimaryKeySelective(BaseTimeRange record) {
		return baseTimeRangeMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(BaseTimeRange record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return baseTimeRangeMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(BaseTimeRange record, BaseTimeRangeExample example) {

		return baseTimeRangeMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(BaseTimeRange record, BaseTimeRangeExample example) {

		return baseTimeRangeMapper.updateByExample(record, example);
	}

	@Override
	public List<BaseTimeRange> selectByExample(BaseTimeRangeExample example) {

		return baseTimeRangeMapper.selectByExample(example);
	}


	@Override
	public long countByExample(BaseTimeRangeExample example) {

		return baseTimeRangeMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(BaseTimeRangeExample example) {

		return baseTimeRangeMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param baseTimeRange
	 * @return
	 */
	public int checkNameUnique(BaseTimeRange baseTimeRange){
		BaseTimeRangeExample example=new BaseTimeRangeExample();
		example.createCriteria().andRangeNameEqualTo(baseTimeRange.getRangeName());
		List<BaseTimeRange> list=baseTimeRangeMapper.selectByExample(example);
		return list.size();
	}

	/**
	  * 查询全部餐段集合
	  * @return
	  */
	 public List<BaseTimeRange> queryList(){
		 BaseTimeRangeExample rangeExample=new BaseTimeRangeExample();
		 return baseTimeRangeMapper.selectByExample(rangeExample);
	 }
}
