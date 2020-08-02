package com.cpdms.service.dinning;

import java.util.List;
import java.util.Arrays;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;
import com.github.pagehelper.PageInfo;
import com.cpdms.common.base.BaseService;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.dinning.BaseSellTypeMapper;
import com.cpdms.model.dinning.BaseSellType;
import com.cpdms.model.dinning.BaseSellTypeExample;
import com.cpdms.model.dinning.BaseTimeRange;
import com.cpdms.model.dinning.BaseTimeRangeExample;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.util.SnowflakeIdWorker;
import org.springframework.transaction.annotation.Transactional;

/**
 * 菜品供应方式 BaseSellTypeService
 * @Title: BaseSellTypeService.java 
 * @Package com.cpdms.dinning.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:49:53  
 **/
@Service
@Transactional
public class BaseSellTypeService implements BaseService<BaseSellType, BaseSellTypeExample>{
	@Autowired
	private BaseSellTypeMapper baseSellTypeMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<BaseSellType> list(Tablepar tablepar,String name){
	        BaseSellTypeExample testExample=new BaseSellTypeExample();
	        testExample.setOrderByClause("create_date DESC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andSellTypeNameLike("%"+name+"%");
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BaseSellType> list= baseSellTypeMapper.selectByExample(testExample);
	        PageInfo<BaseSellType> pageInfo = new PageInfo<BaseSellType>(list);
	        return  pageInfo;
	 }

	 public List<BaseSellType> getByName(String name) {
	     BaseSellTypeExample testExample=new BaseSellTypeExample();
	     if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andSellTypeNameEqualTo(name);
	        }
	        return baseSellTypeMapper.selectByExample(testExample);
     }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista=Convert.toListStrArray(ids);
			BaseSellTypeExample example=new BaseSellTypeExample();
			example.createCriteria().andIdIn(lista);
			return baseSellTypeMapper.deleteByExample(example);


	}

	/**
	  * 查询全部供应方式集合
	  * @return
	  */
	 public List<BaseSellType> queryList(){
		 BaseSellTypeExample sellTypeExample=new BaseSellTypeExample();
		 return baseSellTypeMapper.selectByExample(sellTypeExample);
	 }


	@Override
	public BaseSellType selectByPrimaryKey(String id) {

			return baseSellTypeMapper.selectByPrimaryKey(id);

	}


	@Override
	public int updateByPrimaryKeySelective(BaseSellType record) {
		return baseSellTypeMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(BaseSellType record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return baseSellTypeMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(BaseSellType record, BaseSellTypeExample example) {

		return baseSellTypeMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(BaseSellType record, BaseSellTypeExample example) {

		return baseSellTypeMapper.updateByExample(record, example);
	}

	@Override
	public List<BaseSellType> selectByExample(BaseSellTypeExample example) {

		return baseSellTypeMapper.selectByExample(example);
	}


	@Override
	public long countByExample(BaseSellTypeExample example) {

		return baseSellTypeMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(BaseSellTypeExample example) {

		return baseSellTypeMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param baseSellType
	 * @return
	 */
	public int checkNameUnique(BaseSellType baseSellType){
		BaseSellTypeExample example=new BaseSellTypeExample();
		example.createCriteria().andSellTypeNameEqualTo(baseSellType.getSellTypeName());
		List<BaseSellType> list=baseSellTypeMapper.selectByExample(example);
		return list.size();
	}


}
