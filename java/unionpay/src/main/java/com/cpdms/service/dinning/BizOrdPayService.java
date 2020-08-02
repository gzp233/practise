package com.cpdms.service.dinning;

import java.util.List;

import com.cpdms.common.base.BaseService;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.dinning.BizOrdPayMapper;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.dinning.BizOrdPay;
import com.cpdms.model.dinning.BizOrdPayExample;
import com.cpdms.util.SnowflakeIdWorker;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;

/**
 * 订单支付 BizOrdPayService
 * @Title: BizOrdPayService.java 
 * @Package com.cpdms.dinning.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-12-27 11:04:59  
 **/
@Service
public class BizOrdPayService implements BaseService<BizOrdPay, BizOrdPayExample> {
	@Autowired
	private BizOrdPayMapper bizOrdPayMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<BizOrdPay> list(Tablepar tablepar, String name){
	        BizOrdPayExample testExample=new BizOrdPayExample();
	        testExample.setOrderByClause("id ASC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andOrdCodeLike("%"+name+"%");
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BizOrdPay> list= bizOrdPayMapper.selectByExample(testExample);
	        PageInfo<BizOrdPay> pageInfo = new PageInfo<BizOrdPay>(list);
	        return  pageInfo;
	 }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista= Convert.toListStrArray(ids);
			BizOrdPayExample example=new BizOrdPayExample();
			example.createCriteria().andIdIn(lista);
			return bizOrdPayMapper.deleteByExample(example);


	}


	@Override
	public BizOrdPay selectByPrimaryKey(String id) {

			return bizOrdPayMapper.selectByPrimaryKey(id);

	}

	public BizOrdPay selectByOrdId(String ordId) {
	 	return bizOrdPayMapper.selectByOrdId(ordId);
	}


	@Override
	public int updateByPrimaryKeySelective(BizOrdPay record) {
		return bizOrdPayMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(BizOrdPay record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return bizOrdPayMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(BizOrdPay record, BizOrdPayExample example) {

		return bizOrdPayMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(BizOrdPay record, BizOrdPayExample example) {

		return bizOrdPayMapper.updateByExample(record, example);
	}

	@Override
	public List<BizOrdPay> selectByExample(BizOrdPayExample example) {

		return bizOrdPayMapper.selectByExample(example);
	}


	@Override
	public long countByExample(BizOrdPayExample example) {

		return bizOrdPayMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(BizOrdPayExample example) {

		return bizOrdPayMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param bizOrdPay
	 * @return
	 */
	public int checkNameUnique(BizOrdPay bizOrdPay){
		BizOrdPayExample example=new BizOrdPayExample();
		example.createCriteria().andOrdCodeEqualTo(bizOrdPay.getOrdCode());
		List<BizOrdPay> list=bizOrdPayMapper.selectByExample(example);
		return list.size();
	}


}
