package com.cpdms.service.dinning;

import java.util.List;
import java.util.Arrays;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;
import com.github.pagehelper.PageInfo;
import com.cpdms.common.base.BaseService;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.dinning.BizOrdFoodMapper;
import com.cpdms.model.dinning.BizOrdFood;
import com.cpdms.model.dinning.BizOrdFoodExample;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.util.SnowflakeIdWorker;

/**
 * 预定订单菜品 BizOrdFoodService
 * @Title: BizOrdFoodService.java 
 * @Package com.cpdms.dinning.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:45:21  
 **/
@Service
public class BizOrdFoodService implements BaseService<BizOrdFood, BizOrdFoodExample>{
	@Autowired
	private BizOrdFoodMapper bizOrdFoodMapper;
	
      	   	      	      	      	      	      	      	
	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<BizOrdFood> list(Tablepar tablepar,String name){
	        BizOrdFoodExample testExample=new BizOrdFoodExample();
	        testExample.setOrderByClause("bof.id ASC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andOrdIdLike("%"+name+"%");
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BizOrdFood> list= bizOrdFoodMapper.selectByExample(testExample);
	        PageInfo<BizOrdFood> pageInfo = new PageInfo<BizOrdFood>(list);
	        return  pageInfo;
	 }

	@Override
	public int deleteByPrimaryKey(String ids) {
				
			List<String> lista=Convert.toListStrArray(ids);
			BizOrdFoodExample example=new BizOrdFoodExample();
			example.createCriteria().andIdIn(lista);
			return bizOrdFoodMapper.deleteByExample(example);
			
				
	}
	
	
	@Override
	public BizOrdFood selectByPrimaryKey(String id) {
				
			return bizOrdFoodMapper.selectByPrimaryKey(id);
				
	}

	
	@Override
	public int updateByPrimaryKeySelective(BizOrdFood record) {
		return bizOrdFoodMapper.updateByPrimaryKeySelective(record);
	}
	
	
	/**
	 * 添加
	 */
	@Override
	public int insertSelective(BizOrdFood record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());
			
				
		return bizOrdFoodMapper.insertSelective(record);
	}
	
	
	@Override
	public int updateByExampleSelective(BizOrdFood record, BizOrdFoodExample example) {
		
		return bizOrdFoodMapper.updateByExampleSelective(record, example);
	}

	
	@Override
	public int updateByExample(BizOrdFood record, BizOrdFoodExample example) {
		
		return bizOrdFoodMapper.updateByExample(record, example);
	}

	@Override
	public List<BizOrdFood> selectByExample(BizOrdFoodExample example) {
		
		return bizOrdFoodMapper.selectByExample(example);
	}

	
	@Override
	public long countByExample(BizOrdFoodExample example) {
		
		return bizOrdFoodMapper.countByExample(example);
	}

	
	@Override
	public int deleteByExample(BizOrdFoodExample example) {
		
		return bizOrdFoodMapper.deleteByExample(example);
	}
	
	/**
	 * 检查name
	 * @param bizOrdFood
	 * @return
	 */
	public int checkNameUnique(BizOrdFood bizOrdFood){
		BizOrdFoodExample example=new BizOrdFoodExample();
		example.createCriteria().andOrdIdEqualTo(bizOrdFood.getOrdId());
		List<BizOrdFood> list=bizOrdFoodMapper.selectByExample(example);
		return list.size();
	}


}
