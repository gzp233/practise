package com.cpdms.service.dinning;

import java.util.List;
import java.util.Arrays;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;
import com.github.pagehelper.PageInfo;
import com.cpdms.common.base.BaseService;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.dinning.RefFoodTimeMapper;
import com.cpdms.model.dinning.RefFoodTime;
import com.cpdms.model.dinning.RefFoodTimeExample;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.util.SnowflakeIdWorker;

/**
 * 菜品餐段关系 RefFoodTimeService
 * @Title: RefFoodTimeService.java 
 * @Package com.cpdms.dinning.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:47:39  
 **/
@Service
public class RefFoodTimeService implements BaseService<RefFoodTime, RefFoodTimeExample>{
	@Autowired
	private RefFoodTimeMapper refFoodTimeMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<RefFoodTime> list(Tablepar tablepar,String name){
	        RefFoodTimeExample testExample=new RefFoodTimeExample();
	        testExample.setOrderByClause("id ASC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andTimeIdLike("%"+name+"%");
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<RefFoodTime> list= refFoodTimeMapper.selectByExample(testExample);
	        PageInfo<RefFoodTime> pageInfo = new PageInfo<RefFoodTime>(list);
	        return  pageInfo;
	 }

	 public List<RefFoodTime> getFoods(List<String> timeIds) {
	     RefFoodTimeExample testExample=new RefFoodTimeExample();
         testExample.createCriteria().andTimeIdIn(timeIds);
         return refFoodTimeMapper.selectByExample(testExample);
     }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista=Convert.toListStrArray(ids);
			RefFoodTimeExample example=new RefFoodTimeExample();
			example.createCriteria().andIdIn(lista);
			return refFoodTimeMapper.deleteByExample(example);


	}


	@Override
	public RefFoodTime selectByPrimaryKey(String id) {

			return refFoodTimeMapper.selectByPrimaryKey(id);

	}


	@Override
	public int updateByPrimaryKeySelective(RefFoodTime record) {
		return refFoodTimeMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(RefFoodTime record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return refFoodTimeMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(RefFoodTime record, RefFoodTimeExample example) {

		return refFoodTimeMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(RefFoodTime record, RefFoodTimeExample example) {

		return refFoodTimeMapper.updateByExample(record, example);
	}

	@Override
	public List<RefFoodTime> selectByExample(RefFoodTimeExample example) {

		return refFoodTimeMapper.selectByExample(example);
	}


	public List<RefFoodTime> selectByFoodId(String id){
		return refFoodTimeMapper.selectByFoodId(id);
	}


	@Override
	public long countByExample(RefFoodTimeExample example) {

		return refFoodTimeMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(RefFoodTimeExample example) {

		return refFoodTimeMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param refFoodTime
	 * @return
	 */
	public int checkNameUnique(RefFoodTime refFoodTime){
		RefFoodTimeExample example=new RefFoodTimeExample();
		example.createCriteria().andTimeIdEqualTo(refFoodTime.getTimeId());
		List<RefFoodTime> list=refFoodTimeMapper.selectByExample(example);
		return list.size();
	}
	public int deleteByTimeIdAndFoodId(String timeId, String foodId) {
		return refFoodTimeMapper.deleteByTimeIdAndFoodId(timeId, foodId);
	}

}
