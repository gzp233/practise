package com.cpdms.service.dinning;

import java.util.List;
import java.util.Arrays;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;
import com.github.pagehelper.PageInfo;
import com.cpdms.common.base.BaseService;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.dinning.RefFoodSellMapper;
import com.cpdms.model.dinning.RefFoodSell;
import com.cpdms.model.dinning.RefFoodSellExample;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.util.SnowflakeIdWorker;

/**
 * 菜品供应方式关系 RefFoodSellService
 * @Title: RefFoodSellService.java 
 * @Package com.cpdms.dinning.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:40:08  
 **/
@Service
public class RefFoodSellService implements BaseService<RefFoodSell, RefFoodSellExample>{
	@Autowired
	private RefFoodSellMapper refFoodSellMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<RefFoodSell> list(Tablepar tablepar,String name){
	        RefFoodSellExample testExample=new RefFoodSellExample();
	        testExample.setOrderByClause("id ASC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andSellTypeIdLike("%"+name+"%");
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<RefFoodSell> list= refFoodSellMapper.selectByExample(testExample);
	        PageInfo<RefFoodSell> pageInfo = new PageInfo<RefFoodSell>(list);
	        return  pageInfo;
	 }

	 public List<RefFoodSell> getFoods(String sellTypeId) {
	     RefFoodSellExample testExample=new RefFoodSellExample();
	     if (sellTypeId != null && !"".equals(sellTypeId)) {
	         testExample.createCriteria().andSellTypeIdEqualTo(sellTypeId);
         }
         return refFoodSellMapper.selectByExample(testExample);
     }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista=Convert.toListStrArray(ids);
			RefFoodSellExample example=new RefFoodSellExample();
			example.createCriteria().andIdIn(lista);
			return refFoodSellMapper.deleteByExample(example);


	}


	@Override
	public RefFoodSell selectByPrimaryKey(String id) {

			return refFoodSellMapper.selectByPrimaryKey(id);

	}


	public List<RefFoodSell> selectByFoodId(String id) {
		return refFoodSellMapper.selectByFoodId(id);
	}

	@Override
	public int updateByPrimaryKeySelective(RefFoodSell record) {
		return refFoodSellMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(RefFoodSell record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return refFoodSellMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(RefFoodSell record, RefFoodSellExample example) {

		return refFoodSellMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(RefFoodSell record, RefFoodSellExample example) {

		return refFoodSellMapper.updateByExample(record, example);
	}

	@Override
	public List<RefFoodSell> selectByExample(RefFoodSellExample example) {

		return refFoodSellMapper.selectByExample(example);
	}


	@Override
	public long countByExample(RefFoodSellExample example) {

		return refFoodSellMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(RefFoodSellExample example) {

		return refFoodSellMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param refFoodSell
	 * @return
	 */
	public int checkNameUnique(RefFoodSell refFoodSell){
		RefFoodSellExample example=new RefFoodSellExample();
		example.createCriteria().andSellTypeIdEqualTo(refFoodSell.getSellTypeId());
		List<RefFoodSell> list=refFoodSellMapper.selectByExample(example);
		return list.size();
	}
	
	public int deleteBySellIdAndFoodId(String sellTypeId, String foodId) {
		return refFoodSellMapper.deleteBySellIdAndFoodId(sellTypeId, foodId);
	}

}
