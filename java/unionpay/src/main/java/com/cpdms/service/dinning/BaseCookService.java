package com.cpdms.service.dinning;

import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.Arrays;

import com.cpdms.mapper.dinning.ImgUrlMapper;
import com.cpdms.model.dinning.ImgUrlExample;
import com.cpdms.shiro.util.ShiroUtils;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;
import com.github.pagehelper.PageInfo;
import com.cpdms.common.base.BaseService;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.dinning.BaseCookMapper;
import com.cpdms.model.dinning.BaseCook;
import com.cpdms.model.dinning.BaseCookExample;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.util.SnowflakeIdWorker;

/**
 * 厨师表 BaseCookService
 * @Title: BaseCookService.java 
 * @Package com.cpdms.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-06 14:25:23  
 **/
@Service
public class BaseCookService implements BaseService<BaseCook, BaseCookExample>{
	@Autowired
	private BaseCookMapper baseCookMapper;
	@Autowired
    private ImgUrlMapper imgUrlMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<BaseCook> list(Tablepar tablepar,String name){
	        BaseCookExample testExample=new BaseCookExample();
	        testExample.setOrderByClause("id ASC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andCookNameLike("%"+name+"%");
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BaseCook> list= baseCookMapper.selectByExample(testExample);
	        PageInfo<BaseCook> pageInfo = new PageInfo<BaseCook>(list);
	        return  pageInfo;
	 }

	 public PageInfo<BaseCook> getCookAndImgs(Tablepar tablepar, String cookName) {
	     PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
        List<BaseCook> list= baseCookMapper.selectCookAndImgs(cookName);
        PageInfo<BaseCook> pageInfo = new PageInfo<BaseCook>(list);
        return  pageInfo;
     }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista=Convert.toListStrArray(ids);
			BaseCookExample example=new BaseCookExample();
			example.createCriteria().andIdIn(lista);
			BaseCook baseCook = new BaseCook();
			baseCook.setStatus(0);
			baseCook.setUpdateDate(new Date());
			// 删除图片
			List<BaseCook> baseCooks = baseCookMapper.selectByExample(example);
			List<String> imgIds = new ArrayList<String>();
			if (baseCooks.size() == 0) {
			    return 0;
            }
            for (BaseCook b : baseCooks) {
                imgIds.add(b.getImgId());
            }
            ImgUrlExample imgUrlExample = new ImgUrlExample();
            imgUrlExample.createCriteria().andImgIdIn(imgIds);
            imgUrlMapper.deleteByExample(imgUrlExample);
			return baseCookMapper.updateByExampleSelective(baseCook, example);


	}


	@Override
	public BaseCook selectByPrimaryKey(String id) {

			return baseCookMapper.selectByPrimaryKey(id);

	}

	public BaseCook selectOne(String id) {

			return baseCookMapper.selectOne(id);

	}


	@Override
	public int updateByPrimaryKeySelective(BaseCook record) {
        record.setUpdateBy(ShiroUtils.getLoginName());
        record.setUpdateDate(new Date());
	     return baseCookMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(BaseCook record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());
			record.setCreateBy(ShiroUtils.getLoginName());
			record.setCreateDate(new Date());
			record.setUpdateBy(ShiroUtils.getLoginName());
			record.setUpdateDate(new Date());


		return baseCookMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(BaseCook record, BaseCookExample example) {

		return baseCookMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(BaseCook record, BaseCookExample example) {

		return baseCookMapper.updateByExample(record, example);
	}

	@Override
	public List<BaseCook> selectByExample(BaseCookExample example) {

		return baseCookMapper.selectByExample(example);
	}


	@Override
	public long countByExample(BaseCookExample example) {

		return baseCookMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(BaseCookExample example) {

		return baseCookMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param baseCook
	 * @return
	 */
	public int checkNameUnique(BaseCook baseCook){
		BaseCookExample example=new BaseCookExample();
		example.createCriteria().andCookNameEqualTo(baseCook.getCookName()).andStatusEqualTo(1);
		List<BaseCook> list=baseCookMapper.selectByExample(example);
		return list.size();
	}


}
