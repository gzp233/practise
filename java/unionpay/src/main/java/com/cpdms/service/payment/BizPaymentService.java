package com.cpdms.service.payment;

import java.util.List;

import com.cpdms.common.base.BaseService;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.payment.BizPaymentMapper;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.payment.BizPayment;
import com.cpdms.model.payment.BizPaymentExample;
import com.cpdms.util.SnowflakeIdWorker;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;

/**
 * 交易记录表 BizPaymentService
 * @Title: BizPaymentService.java 
 * @Package com.cpdms.dinning.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-12-27 11:06:26  
 **/
@Service
public class BizPaymentService implements BaseService<BizPayment, BizPaymentExample> {
	@Autowired
	private BizPaymentMapper bizPaymentMapper;
	
      	   	      	      	      	      	      	      	      	      	      	
	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<BizPayment> list(Tablepar tablepar, String name){
	        BizPaymentExample testExample=new BizPaymentExample();
	        testExample.setOrderByClause("id ASC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andPayTransIdLike("%"+name+"%");
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BizPayment> list= bizPaymentMapper.selectByExample(testExample);
	        PageInfo<BizPayment> pageInfo = new PageInfo<BizPayment>(list);
	        return  pageInfo;
	 }

	@Override
	public int deleteByPrimaryKey(String ids) {
				
			List<String> lista= Convert.toListStrArray(ids);
			BizPaymentExample example=new BizPaymentExample();
			example.createCriteria().andIdIn(lista);
			return bizPaymentMapper.deleteByExample(example);
			
				
	}
	
	
	@Override
	public BizPayment selectByPrimaryKey(String id) {
				
			return bizPaymentMapper.selectByPrimaryKey(id);
				
	}

	
	@Override
	public int updateByPrimaryKeySelective(BizPayment record) {
		return bizPaymentMapper.updateByPrimaryKeySelective(record);
	}
	
	
	/**
	 * 添加
	 */
	@Override
	public int insertSelective(BizPayment record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());
			
				
		return bizPaymentMapper.insertSelective(record);
	}
	
	
	@Override
	public int updateByExampleSelective(BizPayment record, BizPaymentExample example) {
		
		return bizPaymentMapper.updateByExampleSelective(record, example);
	}

	
	@Override
	public int updateByExample(BizPayment record, BizPaymentExample example) {
		
		return bizPaymentMapper.updateByExample(record, example);
	}

	@Override
	public List<BizPayment> selectByExample(BizPaymentExample example) {
		
		return bizPaymentMapper.selectByExample(example);
	}

	
	@Override
	public long countByExample(BizPaymentExample example) {
		
		return bizPaymentMapper.countByExample(example);
	}

	
	@Override
	public int deleteByExample(BizPaymentExample example) {
		
		return bizPaymentMapper.deleteByExample(example);
	}
	
	/**
	 * 检查name
	 * @param bizPayment
	 * @return
	 */
	public int checkNameUnique(BizPayment bizPayment){
		BizPaymentExample example=new BizPaymentExample();
		example.createCriteria().andPayTransIdEqualTo(bizPayment.getPayTransId());
		List<BizPayment> list=bizPaymentMapper.selectByExample(example);
		return list.size();
	}


}
